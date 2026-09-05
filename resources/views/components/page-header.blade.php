@props(['title', 'back' => null, 'live' => false])

<div class="flex items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        @if ($back)
            <a href="{{ $back }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition" aria-label="{{ __('Back') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
    </div>

    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
        {{ $slot }}

        @if ($live)
            {{-- Real status, not decoration: reflects the Echo websocket
                 connection this page actually listens on, and a live clock
                 so "as of when" is never ambiguous on a screen that never
                 gets manually refreshed. --}}
            <span x-data="{
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                    connected: !!(window.Echo && window.Echo.connector?.pusher?.connection?.state === 'connected'),
                    init() {
                        setInterval(() => this.time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000);
                        window.Echo?.connector?.pusher?.connection?.bind('state_change', (s) => this.connected = s.current === 'connected');
                    }
                  }"
                  class="flex items-center gap-3 font-mono tabular-nums">
                <span class="flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full" :class="connected ? 'bg-green-500' : 'bg-gray-400'"></span>
                    <span x-text="connected ? 'Live' : 'Offline'" class="font-sans font-medium"></span>
                </span>
                <span x-text="time"></span>
            </span>
        @endif
    </div>
</div>
