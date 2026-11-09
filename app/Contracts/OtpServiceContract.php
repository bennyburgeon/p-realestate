<?php

namespace App\Contracts;

use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpThrottledException;
use App\Models\User;
use App\Services\Otp\OtpVerificationResult;

interface OtpServiceContract
{
    /**
     * Generate a code for the user's phone, store only its hash, invalidate any earlier
     * pending code, and deliver it.
     *
     * @throws OtpThrottledException
     * @throws OtpDeliveryException
     */
    public function send(User $user, ?string $ipAddress = null): void;

    /**
     * Check a submitted code against the user's latest pending code. A match consumes it.
     *
     * @throws OtpThrottledException
     */
    public function verify(User $user, string $code): OtpVerificationResult;
}
