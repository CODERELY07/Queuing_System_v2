@props(['href' => null, 'variant' => 'primary', 'size' => 'md'])

@php
    // The one button implementation for the whole app. <x-primary-button>,
    // <x-secondary-button> and <x-danger-button> are thin wrappers over
    // this — they used to be three separate hand-maintained class strings
    // (Breeze's uppercase tracking-widest style) alongside this one, which
    // is how the homepage and the admin forms ended up with visibly
    // different buttons.
    //
    // Primary buttons carry a 1px light inner edge and a tight contact
    // shadow so they read as a pressable surface rather than a flat
    // rectangle; every other variant is flat with a hairline border.
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg font-medium '
        . 'transition-[background-color,border-color,color,box-shadow,transform] duration-150 '
        . 'active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:active:scale-100';

    $variants = [
        'primary' => 'bg-brand-700 text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.14),0_1px_2px_rgb(28_27_25/0.22)] hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-500',
        'outline' => 'border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700',
        'danger' => 'bg-rose-700 text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.12),0_1px_2px_rgb(28_27_25/0.22)] hover:bg-rose-800',
        'danger-outline' => 'border border-rose-200 bg-white text-rose-700 hover:bg-rose-50 dark:border-rose-900 dark:bg-transparent dark:text-rose-400 dark:hover:bg-rose-950/40',
        'ghost' => 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100',
    ];

    $sizes = [
        'lg' => 'h-12 px-6 text-base',
        'md' => 'h-10 px-4 text-sm',
        'sm' => 'h-9 px-3.5 text-sm',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        {{ $slot }}
    </button>
@endif
