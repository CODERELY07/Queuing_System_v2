<x-app-layout title="Staff" :search="true">
    <x-page-header title="Staff" subtitle="Who can run a counter, and for which department." />

    <x-panel class="overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 p-4 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <span class="font-mono tabular-nums">{{ $staffs->count() }}</span> staff {{ Str::plural('member', $staffs->count()) }}
            </p>
            <x-primary-button
                size="sm"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'create-staff')">
                <x-icon.plus class="h-4 w-4" />
                {{ __('Add staff') }}
            </x-primary-button>
        </div>

        <div class="overflow-x-auto">
            @include('partials.admin.staff-table', ['staffs' => $staffs])
        </div>
    </x-panel>

    @include('partials.admin.staff-modal', [
        'action' => route('staff.store'),
        'method' => 'POST',
        'staff' => null,
    ])
</x-app-layout>
