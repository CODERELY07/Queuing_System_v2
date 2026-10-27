{{-- The plain dashboard section surface: hairline border and a contact
     shadow, nothing more. Padding isn't baked in since it varies (a stat
     panel wants p-6, a table wrapper wants none), so callers add their own
     via class. --}}
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-800/50']) }}>
    {{ $slot }}
</div>
