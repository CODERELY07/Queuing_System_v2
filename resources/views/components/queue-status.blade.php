@props(['queue'])

{{-- Status badge, plus the priority badge when the ticket is in the
     priority lane — the "state" cell shared by every queue table. --}}
<div class="flex flex-wrap items-center gap-1.5">
    <x-status-badge :status="$queue->status" />
    @if ($queue->priority)
        <x-priority-badge />
    @endif
</div>
