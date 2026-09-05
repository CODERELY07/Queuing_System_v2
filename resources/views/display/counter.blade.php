<x-guest-layout :hide-footer="true">
    <div class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">Now Serving</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Please watch for your ticket number to be called.</p>
    </div>

    {{-- A fixed white board on purpose, independent of the page's own
         light/dark mode: a wall display sits under whatever ambient light
         the room has, and a clinical white card reads as more
         professional here than a dark "departure board" look. --}}
    <div id="serving-list" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" aria-live="polite">
        @for ($i = 0; $i < 3; $i++)
            <div class="rounded-2xl border border-gray-100 bg-white shadow-md overflow-hidden animate-pulse">
                <div class="h-11 bg-gray-100"></div>
                <div class="p-6">
                    <div class="h-16 w-1/2 mx-auto bg-gray-200 rounded mb-4"></div>
                    <div class="h-3 w-1/3 mx-auto bg-gray-200 rounded"></div>
                </div>
            </div>
        @endfor
    </div>

    @if ($services->isNotEmpty())
        {{-- Each department also gets its own full-screen version of this
             same board, for mounting at that department's own waiting
             area instead of everyone sharing this combined one. --}}
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">Open a department's full-screen display</p>
            <div class="flex flex-wrap items-center justify-center gap-2">
                @foreach ($services as $service)
                    <a href="{{ route('display.show', $service) }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:border-brand-300 hover:text-brand-700 dark:hover:text-brand-300 transition">
                        <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                        {{ $service->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-guest-layout>
