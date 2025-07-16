<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header with Create Button -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Staff Management</h3>
                        <x-primary-button 
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'create-staff')"
                            class="bg-green-600 hover:bg-green-700">
                            {{ __('Add New Staff') }}
                        </x-primary-button>
                    </div>

                    <!-- Staff Table -->
                    <div class="overflow-x-auto">
                         @include('partials.admin.staff-table', ['staffs' => $staffs])
                    </div>
                </div>
            </div>
        </div>
    </div>

   @include('partials.admin.staff-modal', [
        'key' => 'Add New Staff',
        'action' => route('staff.store'),
        'method' => 'POST',
        'staff' => null,
    ])

    {{-- @include('partials.admin.staff-modal', [
        'key' => 'Edit Staff',
        'action' => route('staff.update', $staff->id),
        'method' => 'PUT',
        'staff' => $staff,
    ]) --}}

</x-app-layout>