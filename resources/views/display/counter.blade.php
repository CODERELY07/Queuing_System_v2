<x-guest-layout title="Now serving" :hide-footer="true">
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <x-eyebrow-badge>Waiting room</x-eyebrow-badge>
            <h1 class="mt-2 font-display text-4xl font-medium tracking-tight text-gray-900 dark:text-white sm:text-5xl">Now serving</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Please watch for your ticket number to be called.</p>
        </div>

        <div class="flex items-center gap-4">
            <x-live-indicator />
            @include('display.partials.voice-toggle')
        </div>
    </div>

    {{-- A fixed white board on purpose, independent of the page's own
         light/dark mode: a wall display sits under whatever ambient light
         the room has. Skeleton cards until the first fetch lands. --}}
    <div id="serving-list" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" aria-live="polite" aria-busy="true">
        @for ($i = 0; $i < 3; $i++)
            <div class="animate-pulse overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="h-11 border-b border-gray-100 bg-gray-50"></div>
                <div class="px-6 py-10">
                    <div class="mx-auto mb-4 h-14 w-2/5 rounded-lg bg-gray-200"></div>
                    <div class="mx-auto h-3 w-1/3 rounded bg-gray-200"></div>
                </div>
                <div class="h-12 border-t border-gray-100 bg-gray-50"></div>
            </div>
        @endfor
    </div>

    @if ($services->isNotEmpty())
        {{-- Each department also gets its own full-screen version of this
             same board, for mounting at that department's own waiting
             area instead of everyone sharing this combined one. --}}
        <div class="mt-12 border-t border-gray-200 pt-8 dark:border-gray-800">
            <p class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Full-screen display for one department</p>
            <ul class="flex flex-wrap gap-2">
                @foreach ($services as $service)
                    <li>
                        <a href="{{ route('display.show', $service) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-600">
                            <span class="font-mono text-xs text-gray-400">{{ $service->prefix }}</span>
                            {{ $service->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-guest-layout>
