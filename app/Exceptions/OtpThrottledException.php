<?php

namespace App\Exceptions;

use RuntimeException;

class OtpThrottledException extends RuntimeException
{
    public function __construct(public readonly int $retryAfterSeconds, string $message = 'Too many attempts. Please try again later.')
    {
        parent::__construct($message);
    }
}
