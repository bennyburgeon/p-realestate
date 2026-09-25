@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }} &middot; {{ config('app.name') }}</title>
        <meta name="description" content="{{ $description ?? 'Find, sell, rent and post property requirements — a modern real estate marketplace.' }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-slate-900 bg-white pb-16 md:pb-0">
        <div>
            <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">
                Skip to content
            </a>

            <x-marketplace.header />

            <main id="main-content">
                @if (session('status'))
                    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <x-marketplace.footer />

            <x-marketplace.mobile-nav />
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
