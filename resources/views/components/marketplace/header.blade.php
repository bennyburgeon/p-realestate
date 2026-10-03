@props(['floating' => false])

@php
    $navLinks = [
        ['label' => 'Buy', 'route' => 'properties.index', 'params' => ['intent' => 'buy']],
        ['label' => 'Rent', 'route' => 'properties.index', 'params' => ['intent' => 'rent']],
        ['label' => 'Projects', 'route' => 'properties.index', 'params' => ['intent' => 'buy', 'category' => 'projects']],
    ];
@endphp

<header
    x-data="{ mobileOpen: false, scrolled: {{ $floating ? 'false' : 'true' }} }"
    @if ($floating) x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 40)" @endif
    :class="scrolled ? 'bg-white/90 backdrop-blur border-neutral-100 shadow-sm' : 'bg-transparent border-transparent'"
    class="{{ $floating ? 'absolute' : 'sticky' }} inset-x-0 top-0 z-50 border-b transition-colors duration-300"
>
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8 sm:h-20">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 font-display text-lg font-bold tracking-tight transition-colors"
           :class="scrolled ? 'text-primary-800' : 'text-white'">
            <img src="{{ asset('images/logo.png') }}" alt="BHKnow" class="h-9 w-auto object-contain">
            <span>BHKnow</span>
        </a>

        <nav class="hidden items-center gap-1 md:flex">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route'], $link['params']) }}"
                   class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors"
                   :class="scrolled ? 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' : 'text-white/80 hover:text-white'">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 md:flex">
            <a href="{{ route('properties.sell.create') }}"
               class="rounded-full px-3 py-2 text-sm font-semibold transition-colors"
               :class="scrolled ? 'text-neutral-700 hover:bg-neutral-100' : 'text-white/90 hover:text-white'">
                Sell Property
            </a>

            <a href="{{ route('requirements.create') }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-secondary-500 px-4 py-2 text-sm font-bold text-primary-900 shadow-sm shadow-secondary-500/30 transition hover:bg-secondary-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                </svg>
                Post Need
            </a>

            @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold transition-colors"
                            :class="scrolled ? 'text-neutral-700 hover:bg-neutral-100' : 'text-white hover:bg-white/10'">
                        {{ Str::before(auth()->user()->name ?? 'Account', ' ') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-neutral-100 bg-white py-1 shadow-xl">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Dashboard</a>
                        <a href="{{ route('requirements.mine') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">My Requirements</a>
                        <a href="{{ route('properties.mine') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">My Properties</a>
                        <a href="{{ route('favourites.index') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Favourites</a>
                        @if (auth()->user()->hasRole(['super_admin', 'admin']))
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Admin Panel</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Profile Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Log Out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="rounded-full px-3 py-2 text-sm font-semibold transition-colors"
                   :class="scrolled ? 'text-neutral-700 hover:bg-neutral-100' : 'text-white hover:bg-white/10'">Log In</a>
                <a href="{{ route('register') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition-colors"
                   :class="scrolled ? 'bg-primary-800 text-white hover:bg-primary-900' : 'bg-white text-primary-900 hover:bg-white/90'">Sign Up</a>
            @endauth
        </div>

        <button @click="mobileOpen = !mobileOpen" class="rounded-lg p-2 md:hidden" :class="scrolled ? 'text-neutral-700 hover:bg-neutral-100' : 'text-white hover:bg-white/10'" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div x-show="mobileOpen" x-cloak x-transition @click.outside="mobileOpen = false" class="border-t border-neutral-100 bg-white px-4 py-3 md:hidden">
        <div class="flex flex-col gap-1">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route'], $link['params']) }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('properties.sell.create') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Sell Property</a>
            <a href="{{ route('requirements.create') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-secondary-700 hover:bg-neutral-50">+ Post Requirement</a>
            @auth
                <div class="my-1 border-t border-neutral-100"></div>
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Dashboard</a>
                <a href="{{ route('requirements.mine') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">My Requirements</a>
                <a href="{{ route('properties.mine') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">My Properties</a>
                <a href="{{ route('favourites.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Favourites</a>
                @if (auth()->user()->hasRole(['super_admin', 'admin']))
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Admin Panel</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Profile Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Log In</a>
                <a href="{{ route('register') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Sign Up</a>
            @endauth
        </div>
    </div>
</header>
