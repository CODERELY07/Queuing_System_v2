<x-app-layout title="Dashboard">
    <x-page-header title="Overview" subtitle="Everything across every department, today." :live="true" />

    <section aria-label="Right now">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-stat-card label="Being served now" :value="$activeQueue" tone="brand">
                <x-icon.dashboard />
            </x-stat-card>

            <x-stat-card label="Staff online" :value="$activeStaffCount" tone="green">
                <x-icon.staff />
            </x-stat-card>

            <x-stat-card label="Priority waiting" :value="$priorityWaiting" tone="violet">
                <x-icon.bolt />
            </x-stat-card>
        </div>
    </section>

    <section class="mt-10" aria-labelledby="analytics-heading">
        <h2 id="analytics-heading" class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Queueing analytics</h2>

        {{-- 2 across until there's genuinely room for 4 — with the sidebar
             open, a 4-up row at tablet width squeezed the cards until their
             labels wrapped three lines deep and the last one clipped off
             the edge. --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-stat-card label="Tickets today" :value="$totalToday" tone="blue">
                <x-icon.calendar />
            </x-stat-card>

            <x-stat-card label="Finished" :value="$finishedToday" tone="green">
                <x-icon.check-circle />
            </x-stat-card>

            <x-stat-card label="No-shows" :value="$skippedToday" tone="rose">
                <x-icon.x-circle />
            </x-stat-card>

            <x-stat-card label="Avg. turnaround" :value="$avgTurnaroundMinutesToday ? round($avgTurnaroundMinutesToday) . ' min' : '—'" tone="amber">
                <x-icon.clipboard />
            </x-stat-card>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <x-panel class="p-6 lg:col-span-2">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Last 7 days</h3>
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-emerald-500"></span> Finished</span>
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-rose-400"></span> No-shows</span>
                    </div>
                </div>

                @php $maxTotal = max($weeklyTrend->max('total'), 1); @endphp

                <div class="flex h-36 items-end gap-2 border-b border-gray-200 dark:border-gray-700 sm:gap-4" aria-hidden="true">
                    @foreach ($weeklyTrend as $day)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                            <span class="font-mono text-[11px] tabular-nums text-gray-400">{{ $day['total'] ?: '' }}</span>
                            <div class="flex w-full max-w-9 flex-col justify-end overflow-hidden rounded-t-md" style="height: {{ $day['total'] / $maxTotal * 100 }}%">
                                @if ($day['total'] > 0)
                                    <div class="bg-rose-400" style="flex-grow: {{ $day['skipped'] }}" title="No-shows: {{ $day['skipped'] }}"></div>
                                    <div class="bg-emerald-500" style="flex-grow: {{ $day['finished'] }}" title="Finished: {{ $day['finished'] }}"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 flex gap-2 sm:gap-4" aria-hidden="true">
                    @foreach ($weeklyTrend as $day)
                        <span class="flex-1 text-center text-xs text-gray-500 dark:text-gray-400">{{ $day['label'] }}</span>
                    @endforeach
                </div>

                {{-- The chart is decorative to a screen reader; this is the
                     same data in a form it can actually read. --}}
                <table class="sr-only">
                    <caption>Tickets finished and no-shows, last 7 days</caption>
                    <thead><tr><th scope="col">Day</th><th scope="col">Finished</th><th scope="col">No-shows</th></tr></thead>
                    <tbody>
                        @foreach ($weeklyTrend as $day)
                            <tr><th scope="row">{{ $day['label'] }}</th><td>{{ $day['finished'] }}</td><td>{{ $day['skipped'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </x-panel>

            <x-panel class="p-6">
                <h3 class="mb-4 font-medium text-gray-900 dark:text-gray-100">Busiest service today</h3>

                @if ($busiestService)
                    <p class="font-display text-3xl font-medium tracking-tight text-gray-900 dark:text-white">{{ $busiestService->service->name ?? 'N/A' }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-mono tabular-nums">{{ $busiestService->total }}</span> {{ Str::plural('ticket', $busiestService->total) }} so far
                    </p>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No tickets yet today.</p>
                @endif
            </x-panel>
        </div>
    </section>
</x-app-layout>
