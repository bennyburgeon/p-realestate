<nav class="fixed inset-x-0 bottom-0 z-50 grid grid-cols-5 items-end border-t border-slate-200 bg-white/95 backdrop-blur pb-[env(safe-area-inset-bottom)] md:hidden">
    <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('home') ? 'text-emerald-600' : 'text-slate-500' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9" />
        </svg>
        Home
    </a>

    <a href="{{ route('properties.index') }}" class="flex flex-col items-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('properties.*') ? 'text-emerald-600' : 'text-slate-500' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7" stroke-linecap="round" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
        </svg>
        Search
    </a>

    <div class="relative flex flex-col items-center">
        <a href="{{ route('requirements.create') }}"
           class="-mt-6 flex h-14 w-14 items-center justify-center rounded-full bg-amber-500 text-white shadow-lg shadow-amber-500/40 ring-4 ring-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
            </svg>
        </a>
        <span class="pb-2 text-[11px] font-semibold text-amber-600">Post Need</span>
    </div>

    <a href="{{ route('favourites.index') }}" class="flex flex-col items-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('favourites.*') ? 'text-emerald-600' : 'text-slate-500' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.8C.8 7.9 2.6 5 5.6 5c1.7 0 3 .9 3.9 2.2C10.4 5.9 11.7 5 13.4 5c3 0 4.8 2.9 3.1 6.2C14 15.65 12 20 12 20Z" />
        </svg>
        Saved
    </a>

    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex flex-col items-center gap-0.5 py-2 text-[11px] font-medium {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-500' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="3.5" />
            <path stroke-linecap="round" d="M4.5 20c1.4-3.4 4.4-5.5 7.5-5.5s6.1 2.1 7.5 5.5" />
        </svg>
        Account
    </a>
</nav>
