<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts & Tailwind/Alpine Fallback -->
        @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative bg-gray-900">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/hero.png') }}" alt="Lanzarote Background" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/60"></div>
            </div>

            <div class="relative z-10">
                <a href="/">
                    <x-application-logo class="w-24 h-24 fill-current text-white drop-shadow-lg hover:scale-105 transition-transform duration-300" />
                </a>
            </div>

            <!-- Glassmorphism Card -->
            <div class="relative z-10 w-full sm:max-w-md mt-6 px-8 py-8 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg shadow-2xl overflow-hidden sm:rounded-2xl border border-white/20">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
