<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="font-sans antialiased">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:shadow-lg dark:focus:bg-gray-800">
            {{ __('Skip to content') }}
        </a>

        <div
            x-data="{
                sidebarOpen: false,
                sidebarCollapsed: (() => { try { return localStorage.getItem('sidebarCollapsed') === '1'; } catch (e) { return false; } })(),
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    try { localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed ? '1' : '0'); } catch (e) {}
                },
            }"
            @keydown.escape.window="sidebarOpen = false"
            class="flex h-screen h-dvh overflow-hidden"
        >
            <div x-show="sidebarOpen" x-cloak x-transition.opacity
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-gray-950/40 sm:hidden"></div>

            @include('layouts.sidebar-nav')

            <div class="flex min-w-0 flex-1 flex-col overflow-y-auto">
                @include('layouts.topbar', ['search' => $search])

                <main id="main" class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    <div class="mx-auto w-full max-w-6xl">
                        @isset($header)
                            <div class="mb-6">
                                {{ $header }}
                            </div>
                        @endisset

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
