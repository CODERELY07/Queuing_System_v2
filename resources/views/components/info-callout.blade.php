@props(['tone' => 'green'])

@php
    // Only one tone in use today (the reassuring "here's what happens
    // next" green check) — kept as a lookup rather than a hardcoded class
    // so a second tone doesn't mean copy-pasting the whole component.
    $tones = [
        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400',
    ];
    $toneClass = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex items-start gap-3']) }}>
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $toneClass }}">
        <x-icon.check class="h-4 w-4" stroke-width="2.5" />
    </span>
    <p class="text-sm text-gray-700 dark:text-gray-200">
        {{ $slot }}
    </p>
</div>
