<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? "{$title} · ".config('app.name', 'BHKnow') : config('app.name', 'BHKnow') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-neutral-50 px-4 py-10">
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2 font-display text-xl font-bold tracking-tight text-primary-800">
                <img src="{{ asset('images/logo.png') }}" alt="BHKnow" class="h-10 w-auto object-contain">
                <span>BHKnow</span>
            </a>

            <div class="w-full overflow-hidden rounded-3xl border border-neutral-100 bg-white px-6 py-8 shadow-xl shadow-neutral-900/5 sm:max-w-md sm:px-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
