<x-layouts.admin-auth>
    <h1 class="text-center text-lg font-bold text-white">Admin Sign In</h1>
    <p class="mt-1 text-center text-sm text-slate-400">Restricted to BHKnow staff.</p>

    <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-300">Email</label>
            <x-text-input id="email" class="mt-1.5 block w-full border-white/10 bg-slate-900 text-white focus:border-emerald-500 focus:ring-emerald-500"
                           type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-300">Password</label>
            <x-text-input id="password" class="mt-1.5 block w-full border-white/10 bg-slate-900 text-white focus:border-emerald-500 focus:ring-emerald-500"
                           type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-lg bg-emerald-600 py-2.5 text-sm font-bold text-white hover:bg-emerald-500">
            Sign In
        </button>
    </form>
</x-layouts.admin-auth>
