@props(['variant' => 'neutral'])

@php
    // The compact labelled button used on table rows (Call, Delete Old
    // Queues) — same family as <x-cta-button>, just shorter and flat.
    $variants = [
        'neutral' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700',
        'danger' => 'border-rose-200 bg-white text-rose-700 hover:bg-rose-50 dark:border-rose-900 dark:bg-transparent dark:text-rose-400 dark:hover:bg-rose-950/40',
    ];

    $classes = 'inline-flex h-8 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border px-3 text-[13px] font-medium '
        . 'transition-colors duration-150 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 '
        . ($variants[$variant] ?? $variants['neutral']);
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    {{ $slot }}
</button>
