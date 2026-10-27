{{-- The raised "sheet" every auth form and the kiosk ticket sit on — the
     one surface in the app that gets a real lift (multi-layer shadow);
     dashboard sections use the flatter <x-panel> instead. One definition,
     so the radius, hairline border and elevation change in one place. --}}
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-gray-800 sm:p-8']) }}>
    {{ $slot }}
</div>
