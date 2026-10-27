@props(['tone' => 'green'])

@php
    // Only one tone in use today (the reassuring "here's what happens
    // next" check) — kept as a lookup rather than a hardcoded class so a
    // second tone doesn't mean copy-pasting the whole component.
    $tones = [
        'green' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
    ];
    $toneClass = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800']) }}>
    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $toneClass }}">
        <x-icon.check class="h-3.5 w-3.5" stroke-width="2.5" />
    </span>
    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
        {{ $slot }}
    </p>
</div>
