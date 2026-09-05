{{-- The white/dark "surface" every auth form and the kiosk ticket sit in.
     Was hand-copied across 8 files (with a stray typo in one) before this
     — one definition now, one place to change the radius or border. --}}
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm p-6 sm:p-8']) }}>
    {{ $slot }}
</div>
