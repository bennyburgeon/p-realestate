<?php

namespace App\Contracts;

interface OtpServiceContract
{
    /**
     * Generate and dispatch an OTP code to the given phone number.
     *
     * @throws \App\Exceptions\OtpThrottledException
     */
    public function send(string $phone): void;

    /**
     * Verify a submitted OTP code for the given phone number.
     *
     * @throws \App\Exceptions\OtpThrottledException
     */
    public function verify(string $phone, string $code): bool;

    /**
     * Remaining verification attempts before the phone is throttled.
     */
    public function attemptsRemaining(string $phone): int;
}
