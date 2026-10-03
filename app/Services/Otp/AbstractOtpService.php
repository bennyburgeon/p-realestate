<?php

namespace App\Services\Otp;

use App\Contracts\OtpServiceContract;
use App\Exceptions\OtpThrottledException;
use App\Models\OneTimePassword;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Shared storage/rate-limiting/verification logic for every OTP driver.
 * Concrete drivers only need to supply how the code is generated and
 * (optionally) how it's actually delivered to the user.
 */
abstract class AbstractOtpService implements OtpServiceContract
{
    public function send(string $phone): void
    {
        $sendKey = "otp-send:{$phone}";

        if (RateLimiter::tooManyAttempts($sendKey, (int) config('otp.max_sends_per_window'))) {
            throw new OtpThrottledException(RateLimiter::availableIn($sendKey));
        }

        RateLimiter::hit($sendKey, (int) config('otp.send_window_seconds'));

        $code = $this->generateCode();

        OneTimePassword::updateOrCreate(
            ['phone' => $phone],
            [
                'code' => $code,
                'attempts' => 0,
                'expires_at' => now()->addSeconds((int) config('otp.ttl_seconds')),
                'consumed_at' => null,
            ]
        );

        $this->dispatch($phone, $code);
    }

    public function verify(string $phone, string $code): bool
    {
        $verifyKey = "otp-verify:{$phone}";

        if (RateLimiter::tooManyAttempts($verifyKey, (int) config('otp.max_verify_attempts'))) {
            throw new OtpThrottledException(RateLimiter::availableIn($verifyKey));
        }

        $otp = OneTimePassword::query()
            ->where('phone', $phone)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            RateLimiter::hit($verifyKey, (int) config('otp.verify_window_seconds'));

            return false;
        }

        if (! hash_equals($otp->code, $code)) {
            RateLimiter::hit($verifyKey, (int) config('otp.verify_window_seconds'));
            $otp->increment('attempts');

            return false;
        }

        $otp->update(['consumed_at' => now()]);
        RateLimiter::clear($verifyKey);
        RateLimiter::clear("otp-send:{$phone}");

        return true;
    }

    public function attemptsRemaining(string $phone): int
    {
        return max(0, (int) config('otp.max_verify_attempts') - RateLimiter::attempts("otp-verify:{$phone}"));
    }

    /**
     * Generate the code to store for this send.
     */
    abstract protected function generateCode(): string;

    /**
     * Actually deliver the code to the user. No-op by default — the
     * temporary driver relies on the user already knowing the fixed code.
     */
    protected function dispatch(string $phone, string $code): void {}
}
