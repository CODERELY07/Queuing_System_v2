<x-app-layout>
    <x-page-header title="Archived Queues" :back="route('admin.queues')" />

    @include('include.message')

    <x-info-callout class="mb-6">
        Tickets here were removed from the active list by "Delete Old Queues" or a per-ticket delete. They still count
        toward the dashboard analytics. Restore one to put it back on the active list, or delete it permanently.
    </x-info-callout>

    <x-panel class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <x-sortable-th column="queue_number" label="Queue #" default="deleted_at" />
                        <x-sortable-th column="name" label="Name" default="deleted_at" />
                        <x-sortable-th column="status" label="Status" default="deleted_at" />
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Service</th>
                        <x-sortable-th column="deleted_at" label="Archived" default="deleted_at" />
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($queues as $queue)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-200 font-mono tabular-nums">
                                {{ $queue->formattedNumber() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar-initials :name="$queue->name" />
                                    <span class="text-gray-900 dark:text-gray-200">{{ $queue->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <x-status-badge :status="$queue->status" />
                                    @if ($queue->priority)
                                        <x-priority-badge />
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-200">
                                {{ $queue->service->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $queue->deleted_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('queues.restore', $queue->id) }}">
                                        @csrf
                                        <x-row-action-button type="submit">
                                            Restore
                                        </x-row-action-button>
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
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                                Nothing archived.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $queues->links() }}
        </div>
    </x-panel>
</x-app-layout>
