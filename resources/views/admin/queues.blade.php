<x-app-layout>
    <x-page-header title="Queues" :live="true" />

    @include('include.message')

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <x-stat-card label="Awaiting today" :value="$waitingToday" tone="amber">
            <x-icon.calendar class="h-6 w-6" />
        </x-stat-card>
        <x-stat-card label="No-shows today" :value="$skippedToday" tone="rose">
            <x-icon.x-circle class="h-6 w-6" />
        </x-stat-card>
        <x-stat-card label="Finished today" :value="$finishedToday" tone="green">
            <x-icon.check-circle class="h-6 w-6" />
        </x-stat-card>
    </div>

    <x-panel class="overflow-hidden">
        <div class="flex justify-end p-4 border-b border-gray-100 dark:border-gray-700">
            <x-row-action-button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-old-queues')">
                <x-icon.trash class="h-4 w-4" />
                Delete Old Queues
            </x-row-action-button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <x-sortable-th column="queue_number" label="Queue #" />
                        <x-sortable-th column="name" label="Name" />
                        <x-sortable-th column="status" label="Status" />
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Service</th>
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
                            <td class="px-4 py-3">
                                <x-icon-button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-queue-{{ $queue->id }}')" aria-label="{{ __('Delete ticket :number', ['number' => $queue->formattedNumber()]) }}">
                                    <x-icon.trash class="h-4 w-4" />
                                </x-icon-button>

                                <x-confirm-delete-modal
                                    :name="'delete-queue-' . $queue->id"
                                    :action="route('queues.destroy', $queue->id)"
                                    title="Delete this ticket?"
                                    :body="'Ticket ' . $queue->formattedNumber() . ' for ' . $queue->name . ' will be removed permanently.'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                                No queues found.
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

    <x-confirm-delete-modal
        name="delete-old-queues"
        :action="route('queues.deleteOld')"
        title="Delete old queues?"
        body="Every ticket from before today will be removed permanently. Today's queues are not affected." />
</x-app-layout>
