<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <x-sortable-th column="name" label="Name" default="name" />
            <x-sortable-th column="email" label="Email" default="name" />
            <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                Service
            </th>
            <th scope="col" class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse ($staffs as $staff)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <x-avatar-initials :name="$staff->name" />
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $staff->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                    {{ $staff->email }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                    {{ $staff->service->name }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end items-center gap-1">
                        <x-icon-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'edit-staff-{{ $staff->id }}')"
                            aria-label="{{ __('Edit :name', ['name' => $staff->name]) }}">
                            <x-icon.pencil class="h-4 w-4" />
                        </x-icon-button>
                        <x-icon-button
                            variant="danger"
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'delete-staff-{{ $staff->id }}')"
                            aria-label="{{ __('Delete :name', ['name' => $staff->name]) }}">
                            <x-icon.trash class="h-4 w-4" />
                        </x-icon-button>

                        {{-- Modals live inside the cell, not as a direct
                             child of <tr> — a <tr> may only directly contain
                             <td>/<th>, so anything else (this <div>-based
                             modal included) gets silently foster-parented
                             out of the table by the browser's HTML parser,
                             which was corrupting the form inside it. --}}
                        @include('partials.admin.staff-modal', [
                            'key' => 'Edit Staff',
                            'action' => route('staff.update', $staff->id),
                            'method' => 'PUT',
                            'staff' => $staff,
                        ])

                        <x-confirm-delete-modal
                            :name="'delete-staff-' . $staff->id"
                            :action="route('staff.destroy', $staff->id)"
                            :form-id="'staff-delete-' . $staff->id"
                            title="Delete this staff member?"
                            :body="'This removes ' . $staff->name . '\'s login — they will no longer be able to sign in.'" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                    No staff found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
