@props(['variant' => 'neutral'])

@php
    // The small outline pill used on table rows (Call, Edit, Delete) —
    // distinct from <x-cta-button>: no shadow, no focus ring styling,
    // pill-shaped rather than rounded-lg. Was hand-copied across the
    // staff dashboard, admin queues, and staff table with the same two
    // color variants every time.
    $variants = [
        'neutral' => 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200',
        'danger' => 'border-rose-200 dark:border-rose-900 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400',
    ];

    $classes = 'inline-flex items-center justify-center rounded-full border active:scale-[0.98] text-sm font-medium px-4 py-1.5 transition '
        . ($variants[$variant] ?? $variants['neutral']);
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    {{ $slot }}
</button>
