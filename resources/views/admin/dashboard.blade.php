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
</x-app-layout>
