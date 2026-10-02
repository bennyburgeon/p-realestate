<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin Login &middot; {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-100 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-slate-900 px-4 py-10">
            <div class="mb-6 flex items-center gap-2 text-xl font-extrabold tracking-tight text-white">
                <img src="{{ asset('images/logo.png') }}" alt="BHKnow" class="h-10 w-auto object-contain">
                BHKnow Admin
            </div>

            <div class="w-full overflow-hidden rounded-2xl border border-white/10 bg-slate-800 px-6 py-6 shadow-xl sm:max-w-md">
                {{ $slot }}
            </div>

            <a href="{{ route('home') }}" class="mt-6 text-xs font-semibold text-slate-400 hover:text-slate-200">&larr; Back to BHKnow</a>
        </div>
    </body>
</html>
