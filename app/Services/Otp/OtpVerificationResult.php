<?php

namespace App\Services\Otp;

enum OtpVerificationResult
{
    case Verified;
    case Invalid;
    case Expired;
    case NotFound;
    case TooManyAttempts;

    public function message(): string
    {
        return match ($this) {
            self::Verified => 'The OTP has been verified.',
            self::Invalid => 'The OTP is incorrect.',
            self::Expired => 'This OTP has expired. Please request a new one.',
            self::NotFound => 'This OTP is no longer valid. Please request a new one.',
            self::TooManyAttempts => 'Too many incorrect attempts. Please request a new OTP.',
        };
    }
}
