<table class="data-table">
    <thead>
        <tr>
            <x-sortable-th column="name" label="Name" default="name" />
            <x-sortable-th column="email" label="Email" default="name" />
            <th scope="col">Department</th>
            <th scope="col" class="text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($staffs as $staff)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <x-avatar-initials :name="$staff->name" />
                        <span class="font-medium">{{ $staff->name }}</span>
                    </div>
                </td>
                <td class="text-gray-500 dark:text-gray-400">{{ $staff->email }}</td>
                <td class="text-gray-500 dark:text-gray-400">{{ $staff->service->name ?? '—' }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
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
                            'action' => route('staff.update', $staff->id),
                            'method' => 'PUT',
                            'staff' => $staff,
                        ])

                        <x-confirm-delete-modal
                            :name="'delete-staff-' . $staff->id"
                            :action="route('staff.destroy', $staff->id)"
                            ajax
                            title="Delete this staff member?"
                            :body="$staff->name . ' will no longer be able to sign in. This removes their login.'" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-12 text-center text-gray-500 dark:text-gray-400">
                    {{ request('q') ? 'No staff match that search.' : 'No staff yet.' }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
