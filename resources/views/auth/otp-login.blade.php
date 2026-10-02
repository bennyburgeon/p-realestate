<x-guest-layout>
    <div x-data="{ resendIn: 30 }" x-init="if (resendIn > 0) { const t = setInterval(() => { resendIn--; if (resendIn <= 0) clearInterval(t); }, 1000); }">
        <div class="text-center">
            <h1 class="font-display text-2xl font-bold text-primary-900">{{ $phone ? 'Enter your code' : 'Find your place. Start here.' }}</h1>
            <p class="mt-1.5 text-sm text-neutral-500">{{ $phone ? 'We sent a 4-digit code to your phone.' : 'No password needed — just your mobile number.' }}</p>
        </div>

        @if ($phone)
            {{-- Step 2: OTP verification --}}
            <form method="POST" action="{{ route('login.otp.verify') }}" class="mt-8 space-y-5">
                @csrf
                <input type="hidden" name="phone" value="{{ $phone }}">

                <div class="text-center text-sm text-slate-600">
                    Code sent to <span class="font-semibold text-slate-900">+91 {{ $phone }}</span>
                    &mdash;
                    <a href="{{ route('login', ['reset' => 1]) }}" class="font-semibold text-primary-700 hover:underline">change number</a>
                </div>

                <div>
                    <label for="code" class="sr-only">Enter OTP</label>
                    <input type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="4" id="code" name="code"
                           placeholder="&bull; &bull; &bull; &bull;" autofocus
                           class="w-full rounded-xl border-slate-200 text-center text-2xl tracking-[0.75em] focus:border-primary-500 focus:ring-primary-500">
                    <x-input-error :messages="$errors->get('code')" class="mt-2 text-center" />
                </div>

                @if ($isNewUser)
                    <div>
                        <label for="name" class="text-sm font-semibold text-slate-700">Your name</label>
                        <input type="text" id="name" name="name" placeholder="e.g. Akhil Benny"
                               class="mt-1.5 w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                @endif

                <button type="submit"
                        class="w-full rounded-xl bg-primary-600 py-3 text-sm font-bold text-white shadow-sm shadow-primary-600/30 transition hover:bg-primary-700">
                    Verify &amp; Continue
                </button>

                <div class="text-center text-sm">
                    <template x-if="resendIn > 0">
                        <span class="text-slate-400">Resend OTP in <span x-text="resendIn"></span>s</span>
                    </template>
                    <template x-if="resendIn <= 0">
                        <button type="submit" formaction="{{ route('login.otp.send') }}" class="font-semibold text-primary-700 hover:underline">
                            Resend OTP
                        </button>
                    </template>
                </div>
            </form>
        @else
            {{-- Step 1: mobile number entry --}}
            <form method="POST" action="{{ route('login.otp.send') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="phone" class="text-sm font-semibold text-slate-700">Mobile number</label>
                    <div class="mt-1.5 flex overflow-hidden rounded-xl border border-slate-200 focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500">
                        <span class="flex items-center bg-slate-50 px-3 text-sm font-semibold text-slate-500">+91</span>
                        <input type="tel" inputmode="numeric" maxlength="10" id="phone" name="phone" value="{{ old('phone') }}"
                               placeholder="98765 43210" autofocus
                               class="w-full border-0 focus:ring-0">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary-600 py-3 text-sm font-bold text-white shadow-sm shadow-primary-600/30 transition hover:bg-primary-700">
                    Send OTP
                </button>
            </form>
        @endif

        <p class="mt-6 flex items-center justify-center gap-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
            <span>Secure</span>&bull;<span>Simple</span>&bull;<span>Fast</span>
        </p>
    </div>
</x-guest-layout>
