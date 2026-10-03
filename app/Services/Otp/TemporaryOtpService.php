<?php

namespace App\Services\Otp;

/**
 * Development OTP driver — always generates the fixed code from config
 * and never actually delivers it anywhere (the tester already knows it).
 * Active when OTP_DRIVER is unset or "temporary".
 */
class TemporaryOtpService extends AbstractOtpService
{
    protected function generateCode(): string
    {
        return (string) config('otp.fixed_code');
    }
}
