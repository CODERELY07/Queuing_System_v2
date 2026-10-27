@props(['disabled' => false])

{{-- The look (border, radius, focus ring) comes from the shared form-control
     rules in app.css, so this component only adds the disabled state. --}}
<input @disabled($disabled) {{ $attributes->merge(['class' => 'disabled:cursor-not-allowed disabled:bg-gray-50 disabled:opacity-70 dark:disabled:bg-gray-800']) }}>
