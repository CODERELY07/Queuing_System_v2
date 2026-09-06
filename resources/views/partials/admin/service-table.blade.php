<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                Service
            </th>
            <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                Prefix
            </th>
            <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                Staff
            </th>
            <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                Tickets
            </th>
            <th scope="col" class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse ($services as $service)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <x-avatar-initials :name="$service->name" />
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $service->name }}</span>
                            <p class="text-xs text-gray-400 dark:text-gray-500">/display/{{ $service->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 font-mono">
                    {{ $service->prefix }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                    {{ $service->users_count }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                    {{ $service->tickets_count }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end items-center gap-1">
                        <x-icon-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'edit-service-{{ $service->id }}')"
                            aria-label="{{ __('Edit :name', ['name' => $service->name]) }}">
                            <x-icon.pencil class="h-4 w-4" />
                        </x-icon-button>
                        <x-icon-button
                            variant="danger"
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'delete-service-{{ $service->id }}')"
                            aria-label="{{ __('Delete :name', ['name' => $service->name]) }}">
                            <x-icon.trash class="h-4 w-4" />
                        </x-icon-button>

                        {{-- Same reasoning as the staff table: modals live
                             inside the cell, not as a sibling of it, so the
                             browser doesn't foster-parent them out of the
                             table and corrupt the form. --}}
                        @include('partials.admin.service-modal', [
                            'key' => 'Edit Service',
                            'action' => route('services.update', $service->id),
                            'method' => 'PUT',
                            'service' => $service,
                        ])

                        @php
                            $blockers = array_filter([
                                $service->users_count > 0 ? Str::plural('staff member', $service->users_count) . ": {$service->users_count}" : null,
                                $service->tickets_count > 0 ? Str::plural('queue ticket', $service->tickets_count) . ": {$service->tickets_count}" : null,
                            ]);
                        @endphp

                        <x-confirm-delete-modal
                            :name="'delete-service-' . $service->id"
                            :action="route('services.destroy', $service->id)"
                            :form-id="'service-delete-' . $service->id"
                            title="Delete this service?"
                            :body="$service->is_internal
                                ? 'This is a protected system service and can\'t be deleted.'
                                : ($blockers
                                    ? 'This service still has records tied to it (' . implode(', ', $blockers) . ') — reassign or clear those first.'
                                    : $service->name . ' will be removed permanently.')" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                    No services found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
