{{-- The staff ticket table and its pager. Rendered inline by
     staff/dashboard and on its own by StaffController::queueTable() — the
     markup counter.js swaps in when the queue changes — so both are always
     identical. --}}
@php $dashboardUrl = route('dashboard.with_type', ['user_type' => 'staff']); @endphp

<div class="overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <x-sortable-th column="queue_number" label="Ticket" :default="null" :base="$dashboardUrl" />
                <x-sortable-th column="name" label="Name" :default="null" :base="$dashboardUrl" />
                <x-sortable-th column="status" label="Status" :default="null" :base="$dashboardUrl" />
                <th scope="col" class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($queues as $queue)
                <tr @class(['bg-sky-50/50 dark:bg-sky-400/5' => $queue->status === 'serving'])>
                    <td class="font-mono tabular-nums">{{ $queue->formattedNumber() }}</td>
                    <td><x-queue-person :queue="$queue" /></td>
                    <td><x-queue-status :queue="$queue" /></td>
                    <td class="text-right">
                        @if ($queue->status !== 'finish')
                            <x-row-action-button class="selected-call" data-id="{{ $queue->id }}" aria-label="{{ $queue->status === 'serving' ? 'Recall' : 'Call' }} {{ $queue->formattedNumber() }}">
                                {{ $queue->status === 'serving' ? 'Recall' : 'Call' }}
                            </x-row-action-button>
                        @else
                            <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500 dark:text-gray-400">
                        {{ request('q') ? 'No tickets match that search.' : 'No tickets yet today.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($queues->hasPages())
    <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-700">
        {{ $queues->links() }}
    </div>
@endif
