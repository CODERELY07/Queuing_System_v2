@php
    // Central source of truth for how a queue status reads, so the same
    // pill doesn't get hand-rolled per view. Each state gets its own hue —
    // never reused for the brand accent — so "your turn" and "press this"
    // are never the same color. A 1px inset ring (rather than a heavier
    // fill) keeps a column of badges legible without shouting.
    $styles = [
        'waiting' => ['label' => 'Waiting', 'class' => 'bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/25', 'dot' => 'bg-amber-500'],
        'serving' => ['label' => 'Serving', 'class' => 'bg-sky-50 text-sky-800 ring-sky-600/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/25', 'dot' => 'bg-sky-500'],
        'finish' => ['label' => 'Finished', 'class' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/25', 'dot' => 'bg-emerald-500'],
        'skipped' => ['label' => 'No-show', 'class' => 'bg-rose-50 text-rose-800 ring-rose-600/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/25', 'dot' => 'bg-rose-500'],
    ];

    $style = $styles[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-700 ring-gray-500/20 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-500/30', 'dot' => 'bg-gray-400'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {$style['class']}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $style['dot'] }}" aria-hidden="true"></span>
    {{ $style['label'] }}
</span>
