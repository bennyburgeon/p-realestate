<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-10">
            <a href="{{ route('home') }}" class="mb-6 flex items-center gap-2 text-xl font-extrabold tracking-tight text-slate-900">
                <img src="{{ asset('images/logo.png') }}" alt="BHKnow" class="h-10 w-auto object-contain">
                <span>BHKnow</span>
            </a>

            <div class="w-full overflow-hidden rounded-2xl border border-slate-100 bg-white px-6 py-6 shadow-sm sm:max-w-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
