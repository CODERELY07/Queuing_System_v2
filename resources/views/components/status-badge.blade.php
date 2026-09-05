@php
    // Central source of truth for how a queue status reads, so the same
    // pill doesn't get hand-rolled per view. Each state gets its own hue —
    // never reused for the brand accent — so "your turn" and "press this"
    // are never the same color.
    $styles = [
        'waiting' => ['label' => 'Waiting', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300', 'dot' => 'bg-amber-500'],
        'serving' => ['label' => 'Serving', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300', 'dot' => 'bg-blue-500'],
        'finish' => ['label' => 'Finished', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300', 'dot' => 'bg-green-500'],
        'skipped' => ['label' => 'No-show', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300', 'dot' => 'bg-rose-500'],
    ];

    $style = $styles[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'dot' => 'bg-gray-400'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {$style['class']}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $style['dot'] }}" aria-hidden="true"></span>
    {{ $style['label'] }}
</span>
