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
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">
            <div x-show="sidebarOpen" x-cloak x-transition.opacity
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-black/50 sm:hidden"></div>

            @include('layouts.sidebar-nav')

            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                @include('layouts.topbar')

                @isset($header)
                    <div class="px-4 sm:px-6 lg:px-8 pt-8">
                        {{ $header }}
                    </div>
                @endisset

                <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
