<x-app-layout>
    <x-page-header :title="$user->service->name . ' Queue'" :live="true" />

    {{-- Not rendered, just read by queuing.js — the "queue.updated"
         broadcast fires for every department at once, and a staff member
         should only see their own department's dashboard refresh when a
         new ticket lands. --}}
    <div id="staff-dashboard-meta" data-service-id="{{ $user->service_id }}" class="hidden" aria-hidden="true"></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card label="Waiting today" :value="$waitingToday" value-id="waiting-count" tone="amber">
            <x-icon.calendar class="h-6 w-6" />
        </x-stat-card>
        <x-stat-card label="No-shows today" :value="$skippedToday" tone="rose">
            <x-icon.x-circle class="h-6 w-6" />
        </x-stat-card>
        <x-stat-card label="Finished today" :value="$finishedToday" tone="green">
            <x-icon.check-circle class="h-6 w-6" />
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Ticket in focus: the one thing this screen exists to show -->
        <x-panel class="p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">Currently Serving</h3>
                <span id="serving-patient-priority" class="hidden"><x-priority-badge /></span>
            </div>
            {{-- Shown until the first /staff/dashboard-data response
                 arrives, so the screen never sits blank while staff wait to
                 find out whether anyone's actually being served. --}}
            <div id="serving-patient-skeleton" class="py-2 mb-3 space-y-2">
                <div class="h-10 w-32 mx-auto rounded-lg bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                <div class="h-4 w-40 mx-auto rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            </div>
            <h2 id="serving-patient-number" class="hidden font-mono tabular-nums text-4xl font-bold text-center text-brand-600 dark:text-brand-400"></h2>
            <h2 id="serving-patient-name" class="hidden text-sm text-gray-500 dark:text-gray-400 p-2 mb-3 text-center"></h2>

            <div class="space-y-2">
                <x-cta-button id="call-next-btn" class="w-full">
                    Next Patient <span class="font-normal text-brand-100">· Space</span>
                </x-cta-button>
                <div class="grid grid-cols-2 gap-2">
                    <x-cta-button id="call" variant="outline" size="sm">
                        Recall
                    </x-cta-button>
                    <button id="skip-btn"
                            class="bg-white dark:bg-gray-800 border border-rose-200 dark:border-rose-900 hover:bg-rose-50 dark:hover:bg-rose-900/30 active:scale-[0.98] text-rose-600 dark:text-rose-400 font-semibold px-4 py-2 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
                        No-show
                    </button>
                </div>
                <button id="call-prev-btn"
                        class="w-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-[0.98] font-medium px-4 py-2 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                    Previous Patient
                </button>
            </div>
        </x-panel>

        <x-panel class="p-6 lg:col-span-2">
            <h3 class="font-semibold text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide mb-3">Up Next</h3>
            <ul id="recent-activity" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                @for ($i = 0; $i < 3; $i++)
                    <li class="flex items-center justify-between gap-2">
                        <span class="h-4 w-14 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></span>
                    </li>
                @endfor
            </ul>
        </x-panel>
    </div>

    {{-- Stable id so queuing.js can swap this whole panel for a freshly
         fetched copy of itself when a new ticket for this department comes
         in — see the ".queue.updated" listener. --}}
    <x-panel id="queue-table-panel" class="overflow-hidden transition-opacity">
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
                                @if ($queue->status !== 'finish')
                                    <x-row-action-button class="selected-call" data-id="{{ $queue->id }}" id="selected-call-{{ $queue->id }}">
                                        Call
                                    </x-row-action-button>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                @endif
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
</x-app-layout>
