<footer class="mt-16 bg-primary-900">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-10 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center gap-2 font-display text-lg font-bold text-white">
                <img src="{{ asset('images/logo.png') }}" alt="BHKnow" class="h-8 w-auto object-contain">
                BHKnow
            </div>

            <nav class="flex flex-wrap gap-x-8 gap-y-3 text-sm font-medium text-primary-200/70">
                <a href="{{ route('properties.index', ['intent' => 'buy']) }}" class="hover:text-white">Buy</a>
                <a href="{{ route('properties.index', ['intent' => 'rent']) }}" class="hover:text-white">Rent</a>
                <a href="{{ route('requirements.create') }}" class="hover:text-white">Post Requirement</a>
                <a href="{{ route('home') }}#how-it-works" class="hover:text-white">How It Works</a>
                <a href="{{ route('home') }}#faq" class="hover:text-white">FAQ</a>
                <a href="{{ route('register') }}" class="hover:text-white">Create Account</a>
            </nav>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-xs text-primary-300/60 sm:flex-row">
            <p>&copy; {{ now()->year }} BHKnow. All rights reserved.</p>
            <p>Built for buyers, tenants, owners and agents.</p>
        </div>
    </div>
</footer>
