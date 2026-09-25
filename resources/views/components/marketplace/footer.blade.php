<footer class="mt-16 border-t border-slate-100 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 md:grid-cols-5">
            <div class="col-span-2">
                <div class="flex items-center gap-2 text-lg font-extrabold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white text-sm">N</span>
                    Nestly
                </div>
                <p class="mt-3 max-w-xs text-sm text-slate-500">
                    A modern marketplace for buying, renting and finding property &mdash; built around what you actually need.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900">Explore</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-500">
                    <li><a href="{{ route('properties.index', ['intent' => 'buy']) }}" class="hover:text-slate-900">Buy a Property</a></li>
                    <li><a href="{{ route('properties.index', ['intent' => 'rent']) }}" class="hover:text-slate-900">Rent a Property</a></li>
                    <li><a href="{{ route('requirements.create') }}" class="hover:text-slate-900">Post a Requirement</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900">Account</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-500">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-slate-900">Dashboard</a></li>
                    <li><a href="{{ route('favourites.index') }}" class="hover:text-slate-900">Favourites</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-slate-900">Create Account</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900">Company</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-500">
                    <li><a href="{{ route('home') }}#how-it-works" class="hover:text-slate-900">How It Works</a></li>
                    <li><a href="{{ route('home') }}#faq" class="hover:text-slate-900">FAQ</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-slate-200 pt-6 text-xs text-slate-400 sm:flex-row">
            <p>&copy; {{ now()->year }} Nestly. All rights reserved.</p>
            <p>Built for buyers, tenants, owners, agents and developers.</p>
        </div>
    </div>
</footer>
