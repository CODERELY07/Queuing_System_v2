{{-- Pager used by every paginated table. Replaces Laravel's stock Tailwind
     view (published so the colors, radius and focus ring follow the app's
     own tokens instead of the stock blue). Lists "Showing 1–10 of 42" on
     the left and previous / page numbers / next on the right. --}}
@if ($paginator->hasPages())
    @php
        $link = 'inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-sm font-medium transition-colors';
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Showing') }}
            <span class="font-mono tabular-nums text-gray-900 dark:text-gray-100">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
            {{ __('of') }}
            <span class="font-mono tabular-nums text-gray-900 dark:text-gray-100">{{ $paginator->total() }}</span>
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="{{ $link }} cursor-not-allowed text-gray-300 dark:text-gray-600" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <x-icon.arrow-left class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }} text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" aria-label="{{ __('pagination.previous') }}">
                    <x-icon.arrow-left class="h-4 w-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="{{ $link }} text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="{{ $link }} bg-gray-900 font-mono tabular-nums text-white dark:bg-gray-100 dark:text-gray-900">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="{{ $link }} font-mono tabular-nums text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }} text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" aria-label="{{ __('pagination.next') }}">
                    <x-icon.arrow-right class="h-4 w-4" />
                </a>
            @else
                <span class="{{ $link }} cursor-not-allowed text-gray-300 dark:text-gray-600" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <x-icon.arrow-right class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
