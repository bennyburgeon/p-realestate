<?php

namespace App\Exceptions;

use RuntimeException;

class OtpThrottledException extends RuntimeException
{
    public function __construct(public readonly int $retryAfterSeconds, ?string $message = null)
    {
        parent::__construct($message ?? "Too many attempts. Please try again in {$retryAfterSeconds} seconds.");
    }
}
