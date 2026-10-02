<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\OtpServiceContract;
use App\Exceptions\OtpThrottledException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    public function __construct(private readonly OtpServiceContract $otp) {}

    /**
     * Display the mobile number / OTP login screen.
     */
    public function create(): View
    {
        if (request()->boolean('reset')) {
            session()->forget(['otp_phone', 'otp_is_new_user']);
        }

        $phone = session('otp_phone');

        return view('auth.otp-login', [
            'phone' => $phone,
            'isNewUser' => $phone ? session('otp_is_new_user', false) : false,
        ]);
    }

    /**
     * Send (or resend) an OTP to the given mobile number.
     */
    public function sendOtp(SendOtpRequest $request): RedirectResponse
    {
        $phone = $request->string('phone')->value();

        try {
            $this->otp->send($phone);
        } catch (OtpThrottledException $e) {
            throw ValidationException::withMessages([
                'phone' => "Too many attempts. Please try again in {$e->retryAfterSeconds} seconds.",
            ]);
        }

        session([
            'otp_phone' => $phone,
            'otp_is_new_user' => ! User::where('phone', $phone)->exists(),
        ]);

        return redirect()->route('login');
    }

    /**
     * Verify the submitted OTP and log the user in, creating a new account
     * if this is the first time we've seen this phone number.
     */
    public function verifyOtp(VerifyOtpRequest $request): RedirectResponse
    {
        $phone = $request->string('phone')->value();

        try {
            $verified = $this->otp->verify($phone, $request->string('code')->value());
        } catch (OtpThrottledException $e) {
            throw ValidationException::withMessages([
                'code' => "Too many attempts. Please try again in {$e->retryAfterSeconds} seconds.",
            ]);
        }

        if (! $verified) {
            throw ValidationException::withMessages([
                'code' => 'The OTP is incorrect.',
            ]);
        }

        $user = User::where('phone', $phone)->first();
        $isNewUser = ! $user;

        if (! $user) {
            if (! $request->filled('name')) {
                throw ValidationException::withMessages([
                    'name' => 'Please tell us your name.',
                ]);
            }

            $user = User::create([
                'name' => $request->string('name')->value(),
                'phone' => $phone,
                'phone_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);

        $request->session()->forget(['otp_phone', 'otp_is_new_user']);
        $request->session()->regenerate();

        return $isNewUser
            ? redirect()->route('profile.edit')->with('status', 'Welcome to BHKnow! Tell us who you are below to get started.')
            : redirect()->intended(route('dashboard', absolute: false));
    }
}
