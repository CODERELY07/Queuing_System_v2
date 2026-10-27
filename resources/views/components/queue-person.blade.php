@props(['queue'])

{{-- Avatar + name — the "who" cell shared by the staff table and both
     admin queue tables. --}}
<div class="flex min-w-0 items-center gap-3">
    <x-avatar-initials :name="$queue->name" />
    <span class="truncate">{{ $queue->name }}</span>
</div>
