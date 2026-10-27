@props(['name', 'size' => 'md'])

@php
    $words = preg_split('/\s+/', trim($name)) ?: [];
    $initials = mb_strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));

    // Deterministic, not random — the same person gets the same color on
    // every table, every page load, without storing a color anywhere.
    // Muted, low-chroma tints so a table of avatars reads as texture
    // rather than a row of candy-colored dots.
    $palette = [
        'bg-brand-100 text-brand-800 dark:bg-brand-900/60 dark:text-brand-200',
        'bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200',
        'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        'bg-rose-100 text-rose-900 dark:bg-rose-900/40 dark:text-rose-200',
        'bg-sky-100 text-sky-900 dark:bg-sky-900/40 dark:text-sky-200',
        'bg-lime-100 text-lime-900 dark:bg-lime-900/40 dark:text-lime-200',
    ];
    $color = $palette[array_sum(array_map('ord', str_split($name))) % count($palette)];

    $sizes = [
        'sm' => 'h-8 w-8 text-[11px]',
        'md' => 'h-9 w-9 text-xs',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-semibold ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $color]) }} aria-hidden="true">
    {{ $initials ?: '?' }}
</span>
