<x-guest-layout>
    <h1 class="mb-1 text-xl font-bold text-slate-900">Create your account</h1>
    <p class="mb-6 text-sm text-slate-500">Buy, rent, list properties or post what you're looking for.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Mobile Number')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label :value="__('I am a')" />
            <div class="mt-2 grid grid-cols-3 gap-2">
                @foreach (['buyer_tenant' => 'Buyer / Tenant', 'owner' => 'Property Owner', 'agent' => 'Agent / Broker'] as $value => $label)
                    <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 px-2 py-2.5 text-center text-xs font-semibold text-slate-600 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                        <input type="radio" name="account_type" value="{{ $value }}" class="sr-only" {{ old('account_type', 'buyer_tenant') === $value ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a class="text-sm text-slate-500 underline hover:text-slate-900" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="!bg-emerald-600 hover:!bg-emerald-700">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
