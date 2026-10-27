{{-- Small uppercase kicker above a page heading — "Patient intake",
     "Ticket confirmed". Plain tracked text, not a pill: the bordered
     icon-pill version of this was one of the most template-looking things
     on the page. Icon (optional) and label both go in the slot. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-brand-700 dark:text-brand-300']) }}>
    {{ $slot }}
</span>
