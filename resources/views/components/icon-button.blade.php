@props(['variant' => 'neutral'])

@php
    // Icon-only button for table row actions (Edit, Delete) — pairs with
    // <x-row-action-button> for actions that still need a text label.
    // Callers must pass their own `aria-label`; the icon alone carries no
    // accessible name.
    $variants = [
        'neutral' => 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-100',
        'danger' => 'text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-400 dark:hover:bg-rose-950/40 dark:hover:text-rose-300',
    ];

    $classes = 'inline-flex h-8 w-8 items-center justify-center rounded-lg transition-colors duration-150 active:scale-[0.95] '
        . ($variants[$variant] ?? $variants['neutral']);
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    {{ $slot }}
</button>
