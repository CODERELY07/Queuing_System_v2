{{-- Shown next to a status badge, never instead of it — priority is which
     lane a ticket is in, not what stage it's at. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-300']) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-violet-500" aria-hidden="true"></span>
    Priority
</span>
