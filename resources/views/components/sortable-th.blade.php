@props(['column', 'label', 'default' => 'queue_number', 'base' => null])

@php
    // `base`: the page the sort links should point at. Defaults to the
    // current URL — but when this renders inside a partial that's fetched
    // from its own URL (the staff table), "current" is the partial's
    // address, and a sort link built from it would navigate to a bare,
    // unstyled table fragment.
    $base ??= url()->current();

    $active = request('sort', $default) === $column;
    $nextDir = $active && request('dir') === 'asc' ? 'desc' : 'asc';
    $query = array_merge(request()->except(['sort', 'dir', 'page']), ['sort' => $column, 'dir' => $nextDir]);
    $ariaSort = $active ? (request('dir') === 'desc' ? 'descending' : 'ascending') : 'none';
@endphp

<th scope="col" aria-sort="{{ $ariaSort }}">
    <a href="{{ $base . '?' . http_build_query($query) }}" class="inline-flex items-center gap-1 transition-colors hover:text-gray-900 dark:hover:text-white {{ $active ? 'text-gray-900 dark:text-white' : '' }}">
        {{ $label }}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ $active ? 'text-gray-700 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
            @if ($active && request('dir') === 'asc')
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            @endif
        </svg>
    </a>
</th>
