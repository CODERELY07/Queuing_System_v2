<table class="data-table">
    <thead>
        <tr>
            <th scope="col">Service</th>
            <th scope="col">Prefix</th>
            <th scope="col">Staff</th>
            <th scope="col">Tickets</th>
            <th scope="col" class="text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($services as $service)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <x-avatar-initials :name="$service->name" />
                        <div class="min-w-0">
                            <span class="font-medium">{{ $service->name }}</span>
                            @if ($service->is_internal)
                                <span class="ml-1.5 rounded-md bg-gray-100 px-1.5 py-0.5 text-[11px] font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-300">System</span>
                            @else
                                <a href="{{ route('display.show', $service) }}" target="_blank" rel="noopener" class="block truncate font-mono text-xs text-gray-400 transition hover:text-brand-700 dark:text-gray-500 dark:hover:text-brand-300">/display/{{ $service->slug }}</a>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="font-mono text-gray-500 dark:text-gray-400">{{ $service->prefix }}</td>
                <td class="font-mono tabular-nums text-gray-500 dark:text-gray-400">{{ $service->users_count }}</td>
                <td class="font-mono tabular-nums text-gray-500 dark:text-gray-400">{{ $service->tickets_count }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        {{-- The internal "Admin" bucket is a fixture of the
                             system, not something to rename or delete — no
                             actions offered rather than buttons that can only
                             answer "no". --}}
                        @unless ($service->is_internal)
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
                                'action' => route('services.update', $service->id),
                                'method' => 'PUT',
                                'service' => $service,
                            ])

                            @php
                                $blockers = array_filter([
                                    $service->users_count > 0 ? $service->users_count . ' ' . Str::plural('staff member', $service->users_count) : null,
                                    $service->tickets_count > 0 ? $service->tickets_count . ' ' . Str::plural('queue ticket', $service->tickets_count) : null,
                                ]);
                            @endphp

                            <x-confirm-delete-modal
                                :name="'delete-service-' . $service->id"
                                :action="route('services.destroy', $service->id)"
                                ajax
                                title="Delete this service?"
                                :body="$blockers
                                    ? 'This service still has records tied to it (' . implode(', ', $blockers) . ') — reassign or clear those first.'
                                    : $service->name . ' will be removed permanently.'" />
                        @endunless
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">No services yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>
