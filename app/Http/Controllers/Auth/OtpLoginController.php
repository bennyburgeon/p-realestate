<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpThrottledException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\PhoneOtpAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    public function __construct(private readonly PhoneOtpAuthenticationService $phoneLogin) {}

    /**
     * Display the mobile number / OTP login screen.
     */
    public function create(): View
    {
        if (request()->boolean('reset')) {
            session()->forget('otp_phone');
        }

        return view('auth.otp-login', [
            'phone' => session('otp_phone'),
            'expiresInMinutes' => (int) ceil(config('otp.ttl_seconds') / 60),
            'resendCooldownSeconds' => (int) config('otp.resend_cooldown_seconds'),
        ]);
    }

    /**
     * Create or find the user for this number, then send (or resend) an OTP.
     * The response is the same whether or not the number already has an account.
     */
    public function sendOtp(SendOtpRequest $request): RedirectResponse
    {
        $phone = $request->string('phone')->value();

        try {
            $this->phoneLogin->initiate($phone, $request->ip());
        } catch (OtpThrottledException|OtpDeliveryException $e) {
            throw ValidationException::withMessages(['phone' => $e->getMessage()]);
        }

        session(['otp_phone' => $phone]);

        return redirect()->route('login');
    }

    /**
     * Verify the OTP against the database and only then log the user in.
     */
    public function verifyOtp(VerifyOtpRequest $request): RedirectResponse
    {
        try {
            ['user' => $user, 'isNewUser' => $isNewUser] = $this->phoneLogin->verify(
                $request->string('phone')->value(),
                $request->string('code')->value(),
            );
        } catch (OtpThrottledException $e) {
            throw ValidationException::withMessages(['code' => $e->getMessage()]);
        }

        Auth::login($user, remember: true);

        $request->session()->forget('otp_phone');
        $request->session()->regenerate();

        return $isNewUser
            ? redirect()->route('profile.edit')->with('status', 'Welcome to BHKnow! Tell us who you are below to get started.')
            : redirect()->intended(route('dashboard', absolute: false));
    }
}
