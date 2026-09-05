@props(['name'])

@php
    $words = preg_split('/\s+/', trim($name));
    $initials = strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));

    // Deterministic, not random — the same person gets the same color on
    // every table, every page load, without storing a color anywhere.
    $palette = [
        'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300',
        'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
        'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300',
        'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300',
    ];
    $color = $palette[array_sum(array_map('ord', str_split($name))) % count($palette)];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold {$color}"]) }}>
    {{ $initials ?: '?' }}
</span>
