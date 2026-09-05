{{-- Small uppercase icon+text pill — "Digital Queuing System", "Patient
     Intake", "Ticket confirmed". Icon and label both go in the slot so any
     icon component can be dropped in. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border border-brand-200 dark:border-brand-800 bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-xs font-semibold uppercase tracking-wide px-3 py-1.5']) }}>
    {{ $slot }}
</span>
