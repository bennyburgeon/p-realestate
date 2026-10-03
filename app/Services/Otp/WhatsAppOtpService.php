<?php

namespace App\Services\Otp;

use Illuminate\Support\Facades\Log;

/**
 * Live OTP driver — generates a real random 4-digit code per send and
 * delivers it over WhatsApp. Active when OTP_DRIVER=whatsapp.
 *
 * The actual provider call is not wired up yet (no WhatsApp Business API
 * credentials/configuration exist in this project). dispatch() is the one
 * place to plug in a real provider (Meta Cloud API, Gupshup, Twilio, etc.)
 * — storage, rate limiting, and verification above it never need to change.
 */
class WhatsAppOtpService extends AbstractOtpService
{
    protected function generateCode(): string
    {
        return (string) random_int(1000, 9999);
    }

    protected function dispatch(string $phone, string $code): void
    {
        // TODO: replace with a real WhatsApp Business API call, e.g.:
        //   Http::withToken(config('services.whatsapp.token'))
        //       ->post(config('services.whatsapp.endpoint'), [...]);
        Log::info("[WhatsAppOtpService] Would send OTP {$code} to +91{$phone} via WhatsApp.");
    }
}
