<x-app-layout>
    <x-page-header title="Staff Management" />

    <x-panel class="overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $staffs->count() }} staff member{{ $staffs->count() === 1 ? '' : 's' }}</p>
            <x-primary-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'create-staff')">
                {{ __('Add New Staff') }}
            </x-primary-button>
        </div>

        <div class="overflow-x-auto">
             @include('partials.admin.staff-table', ['staffs' => $staffs])
        </div>
    </x-panel>

   @include('partials.admin.staff-modal', [
        'key' => 'Add New Staff',
        'action' => route('staff.store'),
        'method' => 'POST',
        'staff' => null,
    ])

</x-app-layout>
