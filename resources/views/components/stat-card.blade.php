@props(['label', 'value', 'tone' => 'brand', 'valueId' => null])

@php
    // Each tile's icon chip carries its own semantic tone — never the
    // brand accent, so "primary action" and "here's a count" stay visually
    // distinct. Same tone set as the status/priority badges.
    $tones = [
        'brand' => 'bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400',
        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400',
        'green' => 'bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-400',
        'violet' => 'bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-400',
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400',
    ];
    $toneClass = $tones[$tone] ?? $tones['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 p-5 flex items-center gap-4']) }}>
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $toneClass }}">
        {{ $slot }}
    </div>
    <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
        <p @if ($valueId) id="{{ $valueId }}" @endif class="font-mono tabular-nums text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
    </div>
</div>
