@props(['column', 'label', 'default' => 'queue_number'])

@php
    $active = request('sort', $default) === $column;
    $nextDir = $active && request('dir') === 'asc' ? 'desc' : 'asc';
    $query = array_merge(request()->except(['sort', 'dir', 'page']), ['sort' => $column, 'dir' => $nextDir]);
@endphp

<th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
    <a href="{{ url()->current() . '?' . http_build_query($query) }}" class="inline-flex items-center gap-1 hover:text-gray-900 dark:hover:text-white">
        {{ $label }}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $active ? 'text-gray-700 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            @if ($active && request('dir') === 'asc')
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            @endif
        </svg>
    </a>
</th>
