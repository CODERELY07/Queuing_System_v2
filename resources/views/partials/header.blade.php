@php
    // One list, rendered by both the desktop bar and the mobile drawer.
    $links = [
        ['label' => 'Kiosk', 'route' => 'kiosk'],
        ['label' => 'Queue display', 'route' => 'display'],
    ];
@endphp

{{-- Public pages are always light (see the `light` class on <html> in
     layouts/guest), so the bar is plain white with a hairline rule rather
     than a tinted, translucent strip. --}}
<header x-data="{ open: false }" @keydown.escape.window="open = false"
        class="sticky top-0 z-30 border-b border-gray-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <a class="flex shrink-0 items-center gap-2.5" href="{{ route('home') }}" aria-label="MedQueue home">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-white">
                    <x-application-logo class="h-[18px] w-[18px] text-white" />
                </span>
                <span class="text-[15px] font-semibold tracking-tight text-gray-900">MedQueue</span>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       @if (request()->routeIs($link['route'])) aria-current="page" @endif
                       class="rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs($link['route']) ? 'bg-brand-50 text-brand-800' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                @auth
                    <x-cta-button :href="route('dashboard')" size="sm" class="ml-2">Dashboard</x-cta-button>
                @else
                    <x-cta-button :href="route('login')" variant="outline" size="sm" class="ml-2">Staff login</x-cta-button>
                @endauth
            </nav>

            <button type="button" @click="open = ! open" aria-label="Toggle navigation" :aria-expanded="open.toString()"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 md:hidden">
                <x-icon.menu class="h-5 w-5" x-show="!open" />
                <x-icon.x class="h-5 w-5" x-show="open" x-cloak />
            </button>
        </div>

        <nav x-show="open" x-cloak x-transition.origin.top class="space-y-1 pb-4 md:hidden" aria-label="Main (mobile)">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="block rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-brand-700 hover:bg-gray-100">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-brand-700 hover:bg-gray-100">Staff login</a>
            @endauth
        </nav>
    </div>
</header>
