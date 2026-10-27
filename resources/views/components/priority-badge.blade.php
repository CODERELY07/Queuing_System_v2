{{-- Shown next to a status badge, never instead of it — priority is which
     lane a ticket is in, not what stage it's at. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-2.5 py-0.5 text-xs font-medium text-violet-800 ring-1 ring-inset ring-violet-600/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/25']) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-violet-500" aria-hidden="true"></span>
    Priority
</span>
