<?php

namespace App\Exceptions;

use RuntimeException;

class OtpDeliveryException extends RuntimeException
{
    public function __construct(string $message = 'We could not send the OTP right now. Please try again.')
    {
        parent::__construct($message);
    }
}
