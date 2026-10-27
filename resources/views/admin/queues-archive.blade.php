<x-app-layout title="Archived queues" :search="true">
    <x-page-header title="Archived queues" :back="route('admin.queues')" />

    @include('include.message')

    <x-info-callout class="mb-6">
        Tickets here were removed from the active list by "Delete old queues" or a per-ticket delete. They still count
        toward the dashboard analytics. Restore one to put it back on the active list, or delete it permanently.
    </x-info-callout>

    <x-panel class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <x-sortable-th column="queue_number" label="Ticket" default="deleted_at" />
                        <x-sortable-th column="name" label="Name" default="deleted_at" />
                        <x-sortable-th column="status" label="Status" default="deleted_at" />
                        <th scope="col">Service</th>
                        <x-sortable-th column="deleted_at" label="Archived" default="deleted_at" />
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
                            <td class="whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $queue->deleted_at->diffForHumans() }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('queues.restore', $queue->id) }}">
                                        @csrf
                                        <x-row-action-button type="submit">Restore</x-row-action-button>
                                    </form>

                                    <x-icon-button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'purge-queue-{{ $queue->id }}')" aria-label="{{ __('Permanently delete ticket :number', ['number' => $queue->formattedNumber()]) }}">
                                        <x-icon.trash class="h-4 w-4" />
                                    </x-icon-button>

                                    <x-confirm-delete-modal
                                        :name="'purge-queue-' . $queue->id"
                                        :action="route('queues.purge', $queue->id)"
                                        title="Delete this ticket permanently?"
                                        :body="'Ticket ' . $queue->formattedNumber() . ' for ' . $queue->name . ' will be erased for good, including from analytics. This can\'t be undone.'" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                {{ request('q') ? 'No archived tickets match that search.' : 'Nothing archived.' }}
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
</x-app-layout>
