{{-- The plain dashboard section surface — no shadow, unlike <x-card>'s
     form/auth surface. Padding isn't baked in here since it varies (a
     stat panel wants p-6, a table wrapper wants none), so callers add
     their own via class. Was hand-copied across staff/admin dashboards
     and both table wrappers before this. --}}
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50']) }}>
    {{ $slot }}
</div>
