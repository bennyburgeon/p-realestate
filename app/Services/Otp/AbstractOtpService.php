<?php

namespace App\Services\Otp;

use App\Contracts\OtpServiceContract;
use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpThrottledException;
use App\Models\OneTimePassword;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Shared storage/rate-limiting/verification logic for every OTP driver.
 * Concrete drivers only supply how the code is generated and delivered.
 *
 * Codes are stored as hashes, are single use, and each new send invalidates the
 * previous pending code.
 */
abstract class AbstractOtpService implements OtpServiceContract
{
    public function send(User $user, ?string $ipAddress = null): void
    {
        $sendKey = "otp-send:{$user->phone}";

        if (RateLimiter::tooManyAttempts($sendKey, (int) config('otp.max_sends_per_window'))) {
            throw new OtpThrottledException(RateLimiter::availableIn($sendKey));
        }

        $this->ensureCooldownElapsed($user);

        RateLimiter::hit($sendKey, (int) config('otp.send_window_seconds'));

        $code = $this->generateCode();

        $otp = DB::transaction(function () use ($user, $code, $ipAddress): OneTimePassword {
            $user->oneTimePasswords()->pending()->update(['invalidated_at' => now()]);

            return $user->oneTimePasswords()->create([
                'phone' => $user->phone,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addSeconds((int) config('otp.ttl_seconds')),
                'ip_address' => $ipAddress,
            ]);
        });

        if (! $this->dispatch($user->phone, $code)) {
            // The code never reached the user: remove it so it cannot be used and does
            // not start the resend cooldown. The send still counts toward the rate limit.
            $otp->delete();

            throw new OtpDeliveryException;
        }
    }

    public function verify(User $user, string $code): OtpVerificationResult
    {
        $verifyKey = "otp-verify:{$user->phone}";

        if (RateLimiter::tooManyAttempts($verifyKey, (int) config('otp.max_verify_attempts'))) {
            throw new OtpThrottledException(RateLimiter::availableIn($verifyKey));
        }

        $result = DB::transaction(function () use ($user, $code): OtpVerificationResult {
            $otp = $user->oneTimePasswords()
                ->pending()
                ->where('phone', $user->phone)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $otp) {
                return OtpVerificationResult::NotFound;
            }

            if ($otp->isExpired()) {
                $otp->update(['invalidated_at' => now()]);

                return OtpVerificationResult::Expired;
            }

            if (! $otp->matches($code)) {
                $otp->attempts++;

                if ($otp->attempts >= (int) config('otp.max_verify_attempts')) {
                    $otp->invalidated_at = now();
                }

                $otp->save();

                return $otp->invalidated_at ? OtpVerificationResult::TooManyAttempts : OtpVerificationResult::Invalid;
            }

            $otp->update(['consumed_at' => now()]);

            return OtpVerificationResult::Verified;
        });

        if ($result === OtpVerificationResult::Verified) {
            RateLimiter::clear($verifyKey);
            RateLimiter::clear("otp-send:{$user->phone}");
        } else {
            RateLimiter::hit($verifyKey, (int) config('otp.verify_window_seconds'));
        }

        return $result;
    }

    /**
     * @throws OtpThrottledException
     */
    protected function ensureCooldownElapsed(User $user): void
    {
        $lastSentAt = $user->oneTimePasswords()->latest('id')->value('created_at');
        $cooldown = (int) config('otp.resend_cooldown_seconds');

        if ($lastSentAt === null) {
            return;
        }

        $elapsed = (int) abs(now()->diffInSeconds($lastSentAt));

        if ($elapsed < $cooldown) {
            $wait = $cooldown - $elapsed;

            throw new OtpThrottledException($wait, "Please wait {$wait} seconds before requesting a new OTP.");
        }
    }

    /**
     * Generate the plaintext code for this send. It is never stored or logged.
     */
    abstract protected function generateCode(): string;

    /**
     * Deliver the code to the user. Returns false when delivery failed.
     */
    protected function dispatch(string $phone, string $code): bool
    {
        return true;
    }
}
