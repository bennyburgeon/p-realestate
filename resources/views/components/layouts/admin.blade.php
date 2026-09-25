@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} &middot; Admin &middot; {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen">
            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-900 text-slate-300 transition-transform lg:static lg:translate-x-0">
                <div class="flex h-16 items-center gap-2 px-5 text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold">N</span>
                    <span class="font-extrabold">Nestly Admin</span>
                </div>
                <nav class="mt-2 space-y-0.5 px-3">
                    @php
                        $links = [
                            ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                            ['route' => 'admin.properties.index', 'label' => 'Properties'],
                            ['route' => 'admin.requirements.index', 'label' => 'Requirements'],
                            ['route' => 'admin.enquiries.index', 'label' => 'Enquiries'],
                            ['route' => 'admin.categories.index', 'label' => 'Categories'],
                            ['route' => 'admin.amenities.index', 'label' => 'Amenities'],
                            ['route' => 'admin.locations.index', 'label' => 'Locations'],
                            ['route' => 'admin.users.index', 'label' => 'Users & Roles'],
                        ];
                    @endphp
                    @foreach ($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="block rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs($link['route'].'*') ? 'bg-emerald-600 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
                <div class="absolute bottom-0 w-full border-t border-white/10 p-3">
                    <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold hover:bg-white/5 hover:text-white">&larr; Back to Site</a>
                </div>
            </aside>

            <div class="flex-1 lg:pl-0">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <h1 class="text-lg font-bold text-slate-900">{{ $title }}</h1>
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-600">
                        {{ auth()->user()->name }}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-red-600 hover:underline">Log Out</button>
                        </form>
                    </div>
                </header>

                <main class="p-4 sm:p-6">
                    @if (session('status'))
                        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
