@props(['label', 'value', 'tone' => 'brand', 'valueId' => null])

@php
    // The tone only tints the small icon beside the label — never a filled
    // chip — so a row of stats reads as a ledger of numbers, not a row of
    // colored buttons. Same tone set as the status/priority badges.
    $tones = [
        'brand' => 'text-brand-600 dark:text-brand-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'rose' => 'text-rose-600 dark:text-rose-400',
        'green' => 'text-emerald-600 dark:text-emerald-400',
        'violet' => 'text-violet-600 dark:text-violet-400',
        'blue' => 'text-sky-600 dark:text-sky-400',
    ];
    $toneClass = $tones[$tone] ?? $tones['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-800/50']) }}>
    <div class="flex items-center justify-between gap-3">
        <p class="truncate text-[13px] font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
        {{-- Forces the icon to one size no matter what the caller passes. --}}
        <span class="shrink-0 {{ $toneClass }} [&>svg]:h-[18px] [&>svg]:w-[18px]" aria-hidden="true">{{ $slot }}</span>
    </div>
    <p @if ($valueId) id="{{ $valueId }}" @endif class="mt-3 truncate font-mono text-3xl font-medium tabular-nums tracking-tight text-gray-900 dark:text-white">{{ $value }}</p>
</div>
