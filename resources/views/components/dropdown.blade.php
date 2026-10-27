@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-800'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" @close.stop="open = false">
    <div @click="open = ! open" :aria-expanded="open.toString()">
        {{ $trigger }}
    </div>

    <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-xl shadow-lg {{ $alignmentClasses }}"
            role="menu"
            @click="open = false">
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
