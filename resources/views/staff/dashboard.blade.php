<x-app-layout :title="$user->service->name . ' counter'" :search="true">
    <x-page-header :title="$user->service->name" subtitle="Counter console" :live="true" />

    {{-- Not rendered, just read by counter.js — the "queue.updated"
         broadcast fires for every department at once, and a staff member
         should only see their own department's dashboard refresh when a
         new ticket lands. --}}
    <div id="staff-dashboard-meta" data-service-id="{{ $user->service_id }}" class="hidden" aria-hidden="true"></div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- The ticket in focus: the one thing this screen exists to show --}}
        <x-panel class="p-6 lg:col-span-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Now serving</h2>
                <span id="serving-patient-priority" class="hidden"><x-priority-badge /></span>
            </div>

            {{-- Shown until the first /staff/dashboard-data response
                 arrives, so the screen never sits blank while staff wait to
                 find out whether anyone's actually being served. --}}
            <div id="serving-patient-skeleton" class="mb-5 space-y-3 py-2" aria-hidden="true">
                <div class="h-14 w-40 animate-pulse rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                <div class="h-4 w-48 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></div>
            </div>
            <div class="mb-5" aria-live="polite">
                <p id="serving-patient-number" class="hidden font-mono text-6xl font-medium tabular-nums tracking-tight text-gray-900 dark:text-white"></p>
                <p id="serving-patient-name" class="mt-1 hidden truncate text-base text-gray-500 dark:text-gray-400"></p>
            </div>

            <div class="space-y-2">
                <x-cta-button id="call-next-btn" size="lg" class="w-full">
                    Next patient
                    <kbd class="rounded bg-white/15 px-1.5 py-0.5 font-sans text-[11px] font-medium text-white/80">Space</kbd>
                </x-cta-button>

                <div class="grid grid-cols-2 gap-2">
                    <x-cta-button id="call" variant="outline">Recall</x-cta-button>
                    <x-cta-button id="skip-btn" variant="danger-outline">No-show</x-cta-button>
                </div>

                <x-cta-button id="call-prev-btn" variant="ghost" class="w-full">Previous patient</x-cta-button>
            </div>
        </x-panel>

        <div class="space-y-6 lg:col-span-7">
            <x-panel class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-gray-700">
                @foreach ([
                    ['id' => 'waiting-count', 'label' => 'Waiting', 'value' => $waitingToday],
                    ['id' => 'skipped-count', 'label' => 'No-shows', 'value' => $skippedToday],
                    ['id' => 'finished-count', 'label' => 'Finished', 'value' => $finishedToday],
                ] as $stat)
                    <div class="px-5 py-4">
                        <p class="text-[13px] font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p id="{{ $stat['id'] }}" class="mt-1 font-mono text-3xl font-medium tabular-nums tracking-tight text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </x-panel>

            <x-panel class="p-6">
                <h2 class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Up next</h2>
                <ul id="recent-activity" class="-mx-3 space-y-1 text-sm text-gray-700 dark:text-gray-300" aria-live="polite">
                    @for ($i = 0; $i < 3; $i++)
                        <li class="px-3 py-2">
                            <span class="block h-5 w-16 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                        </li>
                    @endfor
                </ul>
            </x-panel>
        </div>
    </div>

    {{-- Stable id so counter.js can swap this panel's contents for a freshly
         rendered copy when something changes in this department — see
         StaffController::queueTable(). --}}
    <x-panel id="queue-table-panel" class="overflow-hidden transition-opacity">
        @include('staff.partials.queue-table')
    </x-panel>
</x-app-layout>
