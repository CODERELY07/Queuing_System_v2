<x-app-layout title="Queues" :search="true">
    <x-page-header title="Queues" subtitle="Every ticket across every department." :live="true" />

    @include('include.message')

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Waiting today" :value="$waitingToday" tone="amber">
            <x-icon.calendar />
        </x-stat-card>
        <x-stat-card label="No-shows today" :value="$skippedToday" tone="rose">
            <x-icon.x-circle />
        </x-stat-card>
        <x-stat-card label="Finished today" :value="$finishedToday" tone="green">
            <x-icon.check-circle />
        </x-stat-card>
    </div>

    <x-panel class="overflow-hidden">
        <div class="flex items-center justify-end gap-3 border-b border-gray-100 p-4 dark:border-gray-700">
            <a href="{{ route('queues.archived') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100">
                View archive
                @if ($archivedCount > 0)
                    <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-100 px-1.5 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $archivedCount }}</span>
                @endif
            </a>
            <x-row-action-button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-old-queues')">
                <x-icon.trash class="h-4 w-4" />
                Delete old queues
            </x-row-action-button>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <x-sortable-th column="queue_number" label="Ticket" />
                        <x-sortable-th column="name" label="Name" />
                        <x-sortable-th column="status" label="Status" />
                        <th scope="col">Service</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($queues as $queue)
                        <tr>
                            <td class="font-mono tabular-nums">{{ $queue->formattedNumber() }}</td>
                            <td><x-queue-person :queue="$queue" /></td>
                            <td><x-queue-status :queue="$queue" /></td>
                            <td>{{ $queue->service->name ?? 'N/A' }}</td>
                            <td class="text-right">
                                <x-icon-button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-queue-{{ $queue->id }}')" aria-label="{{ __('Delete ticket :number', ['number' => $queue->formattedNumber()]) }}">
                                    <x-icon.trash class="h-4 w-4" />
                                </x-icon-button>

                                <x-confirm-delete-modal
                                    :name="'delete-queue-' . $queue->id"
                                    :action="route('queues.destroy', $queue->id)"
                                    title="Delete this ticket?"
                                    :body="'Ticket ' . $queue->formattedNumber() . ' for ' . $queue->name . ' will be moved to the archive — you can restore it from there.'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                {{ request('q') ? 'No tickets match that search.' : 'No tickets yet.' }}
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
    </x-panel>

    <x-confirm-delete-modal
        name="delete-old-queues"
        :action="route('queues.deleteOld')"
        title="Delete old queues?"
        body="Every ticket from before today will be cleared from this list. Today's queues are not affected, and the records are kept for analytics and reporting." />
</x-app-layout>
