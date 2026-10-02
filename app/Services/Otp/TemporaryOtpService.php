<?php

namespace App\Services\Otp;

use App\Contracts\OtpServiceContract;
use App\Exceptions\OtpThrottledException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Temporary development OTP implementation. Always "sends" the fixed code
 * from config instead of calling a real SMS/WhatsApp provider. Replace the
 * binding in AppServiceProvider with a WhatsAppOtpService (same contract)
 * once a real provider is integrated — no other code needs to change.
 */
class TemporaryOtpService implements OtpServiceContract
{
    public function send(string $phone): void
    {
        $sendKey = "otp-send:{$phone}";

        if (RateLimiter::tooManyAttempts($sendKey, (int) config('otp.max_sends_per_window'))) {
            throw new OtpThrottledException(RateLimiter::availableIn($sendKey));
        }

        RateLimiter::hit($sendKey, (int) config('otp.send_window_seconds'));

        Cache::put(
            $this->cacheKey($phone),
            ['code' => (string) config('otp.fixed_code'), 'attempts' => 0],
            now()->addSeconds((int) config('otp.ttl_seconds'))
        );
    }

    public function verify(string $phone, string $code): bool
    {
        $verifyKey = "otp-verify:{$phone}";

        if (RateLimiter::tooManyAttempts($verifyKey, (int) config('otp.max_verify_attempts'))) {
            throw new OtpThrottledException(RateLimiter::availableIn($verifyKey));
        }

        $entry = Cache::get($this->cacheKey($phone));

        if (! $entry) {
            RateLimiter::hit($verifyKey, (int) config('otp.verify_window_seconds'));

            return false;
        }

        if (! hash_equals($entry['code'], $code)) {
            RateLimiter::hit($verifyKey, (int) config('otp.verify_window_seconds'));

            return false;
        }

        Cache::forget($this->cacheKey($phone));
        RateLimiter::clear($verifyKey);
        RateLimiter::clear("otp-send:{$phone}");

        return true;
    }

    public function attemptsRemaining(string $phone): int
    {
        return max(0, (int) config('otp.max_verify_attempts') - RateLimiter::attempts("otp-verify:{$phone}"));
    }

    private function cacheKey(string $phone): string
    {
        return "otp:{$phone}";
    }
}
