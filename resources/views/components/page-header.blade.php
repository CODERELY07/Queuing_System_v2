@props(['title', 'subtitle' => null, 'back' => null, 'live' => false])

<div class="mb-6 flex flex-wrap items-start justify-between gap-x-6 gap-y-3 sm:mb-8">
    <div class="flex min-w-0 items-center gap-3">
        @if ($back)
            <a href="{{ $back }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800" aria-label="{{ __('Back') }}">
                <x-icon.arrow-left class="h-4 w-4" />
            </a>
        @endif

        <div class="min-w-0">
            <h1 class="truncate text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
        {{ $slot }}

        @if ($live)
            <x-live-indicator />
        @endif
    </div>
</div>
