@props(['href' => null, 'variant' => 'primary', 'size' => 'md'])

@php
    // The app's public-facing call-to-action button — distinct from
    // <x-primary-button>/<x-secondary-button>, which are the small
    // uppercase Breeze-style buttons used in admin forms. This one was
    // being hand-copied (padding, shadow, focus ring and all) across the
    // homepage, kiosk, ticket, staff dashboard, and admin index.
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition active:scale-[0.98]';

    $variants = [
        'primary' => 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-600/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900',
        'outline' => 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500',
    ];

    $sizes = [
        'lg' => 'text-base py-3 px-6',
        'md' => 'py-2.5 px-5',
        'sm' => 'py-2 px-4',
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
