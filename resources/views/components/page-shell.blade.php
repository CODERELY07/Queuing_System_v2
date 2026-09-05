@props(['maxWidth' => 'max-w-md'])

{{-- The centered, fade-in wrapper every guest-facing form/content page
     starts with (auth forms, kiosk). Not used by the homepage, which has
     its own full-width hero grid — see x-guest-layout's `flush` prop for
     why the surrounding layout padding differs there too. --}}
<div {{ $attributes->merge(['class' => "{$maxWidth} mx-auto animate-fade-up"]) }}>
    {{ $slot }}
</div>
