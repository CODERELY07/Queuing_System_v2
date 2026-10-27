<!DOCTYPE html>
{{-- `light`: public pages never switch to dark (see darkMode in tailwind.config.js). --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head', ['title' => $title, 'scheme' => 'light'])
    </head>
    <body class="font-sans antialiased">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:shadow-lg dark:focus:bg-gray-800">
            {{ __('Skip to content') }}
        </a>

        <div class="flex min-h-screen flex-col">
            @unless ($hideHeader)
                @include('partials.header')
            @endunless

            {{-- A dedicated department display has no header to butt up
                 against and is meant to fill a mounted screen edge to edge,
                 so it also drops the usual max-w-6xl content cap. --}}
            <main id="main" class="w-full flex-1 {{ $hideHeader ? '' : 'mx-auto max-w-6xl px-4 sm:px-6 lg:px-8' }} {{ $flush ? '' : 'py-10 sm:py-14' }}">
                {{ $slot }}
            </main>

            @unless ($hideFooter)
                @include('partials.footer')
            @endunless
        </div>
    </body>
</html>
