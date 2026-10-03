<?php

namespace App\Providers;

use App\Contracts\OtpServiceContract;
use App\Services\Otp\TemporaryOtpService;
use App\Services\Otp\WhatsAppOtpService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OtpServiceContract::class, match (config('otp.driver')) {
            'whatsapp' => WhatsAppOtpService::class,
            default => TemporaryOtpService::class,
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
