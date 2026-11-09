<?php

namespace App\Services;

use App\Contracts\OtpServiceContract;
use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpThrottledException;
use App\Models\Status;
use App\Models\User;
use App\Services\Otp\OtpVerificationResult;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Phone + OTP login, database-driven end to end.
 *
 * initiate(): the user record exists before any code is sent. An unknown number gets a
 * new user with status USER_INACTIVE; a known number reuses its user untouched.
 *
 * verify(): the code is checked against the stored hash. Only then is an eligible
 * Inactive user activated. Suspended users (is_active = false) are refused. It never
 * logs anyone in: the controller does that after verify() returns.
 */
class PhoneOtpAuthenticationService
{
    public function __construct(private readonly OtpServiceContract $otp) {}

    /**
     * @throws OtpThrottledException
     * @throws OtpDeliveryException
     */
    public function initiate(string $phone, ?string $ipAddress = null): void
    {
        // Serialise concurrent requests for one number so they cannot race to create the
        // user or issue two live codes. users.phone is also unique as a final guard.
        $lock = Cache::lock("otp-login:{$phone}", 15);

        try {
            $lock->block(5);
        } catch (LockTimeoutException) {
            throw new OtpThrottledException(5, 'Please wait a moment and try again.');
        }

        try {
            $this->otp->send($this->findOrCreatePendingUser($phone), $ipAddress);
        } finally {
            $lock->release();
        }
    }

    /**
     * @return array{user: User, isNewUser: bool}
     *
     * @throws ValidationException
     * @throws OtpThrottledException
     */
    public function verify(string $phone, string $code): array
    {
        // Failures must keep their attempt counters, so they are returned out of the
        // transaction and only thrown once it has committed.
        [$user, $isNewUser, $error] = DB::transaction(function () use ($phone, $code): array {
            $user = User::where('phone', $phone)->lockForUpdate()->first();

            if (! $user) {
                return [null, false, OtpVerificationResult::NotFound->message()];
            }

            $result = $this->otp->verify($user, $code);

            if ($result !== OtpVerificationResult::Verified) {
                return [null, false, $result->message()];
            }

            if (! $user->is_active) {
                return [null, false, 'This account has been suspended. Please contact support.'];
            }

            if ($user->isAwaitingPhoneVerification()) {
                $user->forceFill(['status_id' => Status::USER_ACTIVE, 'phone_verified_at' => now()])->save();

                return [$user, true, null];
            }

            if ($user->phone_verified_at === null) {
                $user->forceFill(['phone_verified_at' => now()])->save();
            }

            return [$user, false, null];
        });

        if ($error !== null) {
            throw ValidationException::withMessages(['code' => $error]);
        }

        return ['user' => $user, 'isNewUser' => $isNewUser];
    }

    private function findOrCreatePendingUser(string $phone): User
    {
        $user = User::firstWhere('phone', $phone);

        if ($user) {
            return $user;
        }

        try {
            return User::forceCreate([
                'phone' => $phone,
                'status_id' => Status::USER_INACTIVE,
                'is_active' => true,
            ]);
        } catch (UniqueConstraintViolationException) {
            return User::where('phone', $phone)->firstOrFail();
        }
    }
}
