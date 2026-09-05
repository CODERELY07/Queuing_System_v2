@props(['variant' => 'neutral'])

@php
    // Icon-only round button for table row actions (Edit, Delete) — pairs
    // with <x-row-action-button> for actions that still need a text label
    // (Call). Callers must pass their own `aria-label`; the icon alone
    // carries no accessible name.
    $variants = [
        'neutral' => 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700',
        'danger' => 'text-rose-500 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/30',
    ];

    $classes = 'inline-flex h-9 w-9 items-center justify-center rounded-full transition active:scale-[0.95] '
        . ($variants[$variant] ?? $variants['neutral']);
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    {{ $slot }}
</button>
