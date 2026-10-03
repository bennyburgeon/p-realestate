<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Driver
    |--------------------------------------------------------------------------
    |
    | "temporary" (default) always uses the fixed code below and never
    | actually sends anything — for local/dev testing. Set OTP_DRIVER=
    | whatsapp to generate a real random code and deliver it over WhatsApp
    | (the provider call itself still needs real credentials wired into
    | WhatsAppOtpService::dispatch() before it's live). The rest of the
    | application only depends on OtpServiceContract, so no controller or
    | view changes are needed when switching drivers.
    |
    */
    'driver' => env('OTP_DRIVER', 'temporary'),

    'fixed_code' => env('OTP_FIXED_CODE', '1234'),

    'ttl_seconds' => env('OTP_TTL_SECONDS', 300),

    'max_sends_per_window' => env('OTP_MAX_SENDS_PER_WINDOW', 3),

    'send_window_seconds' => env('OTP_SEND_WINDOW_SECONDS', 600),

    'max_verify_attempts' => env('OTP_MAX_VERIFY_ATTEMPTS', 5),

    'verify_window_seconds' => env('OTP_VERIFY_WINDOW_SECONDS', 600),

];
