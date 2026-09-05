<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230D9488' stroke-width='2'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M4 12h4l2-6 3 12 2-6h5'/%3E%3C/svg%3E">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|ibm-plex-mono:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
            @unless ($hideHeader)
                @include('partials.header')
            @endunless

            {{-- A dedicated department display has no header to butt up
                 against and is meant to fill a mounted screen edge to edge,
                 so it also drops the usual max-w-7xl content cap. --}}
            <main class="flex-1 w-full {{ $hideHeader ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }} {{ $flush ? '' : 'py-12 sm:py-16' }}">
                {{ $slot }}
            </main>

            @unless ($hideFooter)
                @include('partials.footer')
            @endunless
        </div>
    </body>
</html>
