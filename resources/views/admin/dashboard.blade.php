<x-app-layout>
    <x-page-header title="Admin Dashboard" :live="true" />

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <x-stat-card label="Active Queues" :value="$activeQueue" tone="brand">
            <x-icon.dashboard class="h-6 w-6" />
        </x-stat-card>

        <x-stat-card label="Staff Active" :value="$activeStaffCount" tone="green">
            <x-icon.staff class="h-6 w-6" />
        </x-stat-card>

        <x-stat-card label="Priority Waiting" :value="$priorityWaiting" tone="violet">
            <x-icon.bolt class="h-6 w-6" />
        </x-stat-card>
    </div>

    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-10 mb-4">Queueing Analytics</h2>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card label="Tickets Today" :value="$totalToday" tone="blue">
            <x-icon.calendar class="h-6 w-6" />
        </x-stat-card>

        <x-stat-card label="Finished Today" :value="$finishedToday" tone="green">
            <x-icon.check-circle class="h-6 w-6" />
        </x-stat-card>

        <x-stat-card label="No-shows Today" :value="$skippedToday" tone="rose">
            <x-icon.x-circle class="h-6 w-6" />
        </x-stat-card>

        <x-stat-card label="Avg. Turnaround" :value="$avgTurnaroundMinutesToday ? round($avgTurnaroundMinutesToday) . ' min' : '—'" tone="amber">
            <x-icon.clipboard class="h-6 w-6" />
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <x-panel class="lg:col-span-2 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-medium text-gray-900 dark:text-gray-100">Last 7 days</h3>
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-green-400 dark:bg-green-500"></span> Finished</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-rose-400 dark:bg-rose-500"></span> No-shows</span>
                </div>
            </div>

            @php
                $maxTotal = max($weeklyTrend->max('total'), 1);
            @endphp

            <div class="flex items-end gap-3 h-32">
                @foreach ($weeklyTrend as $day)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full">
                        <div class="w-full h-full flex flex-col justify-end">
                            <div class="w-full max-w-8 mx-auto bg-rose-400 dark:bg-rose-500 {{ $day['finished'] === 0 ? 'rounded-t-sm' : '' }}"
                                 style="height: {{ $day['skipped'] / $maxTotal * 100 }}%"
                                 title="No-shows: {{ $day['skipped'] }}"></div>
                            <div class="w-full max-w-8 mx-auto bg-green-400 dark:bg-green-500 rounded-t-sm"
                                 style="height: {{ $day['finished'] / $maxTotal * 100 }}%"
                                 title="Finished: {{ $day['finished'] }}"></div>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-panel>

        <x-panel class="p-6">
            <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">Busiest Service Today</h3>

            @if ($busiestService)
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-400">
                        <x-icon.dashboard class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-gray-900 dark:text-gray-200 font-medium">{{ $busiestService->service->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $busiestService->total }} {{ Str::plural('ticket', $busiestService->total) }} today</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No tickets yet today.</p>
            @endif
        </x-panel>
    </div>
</x-app-layout>
