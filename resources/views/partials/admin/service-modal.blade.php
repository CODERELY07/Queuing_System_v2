@php
    // Unique field ids per row — see staff-modal.blade.php.
    $uid = $service ? "service-{$service->id}" : 'service-new';
@endphp

<x-ajax-form-modal
    :name="$service ? 'edit-service-' . $service->id : 'create-service'"
    :title="$service ? 'Edit service' : 'Add service'"
    :action="$action"
    :method="$method"
    :submit="$service ? 'Save changes' : 'Add service'">

    <div>
        <x-input-label :for="$uid . '-name'" value="Name" />
        <x-text-input :id="$uid . '-name'" class="mt-1 block w-full" type="text" name="name" maxlength="100"
                      :value="$service?->name" autocomplete="off" required />
    </div>

    <div>
        <x-input-label :for="$uid . '-prefix'" value="Ticket prefix" />
        <x-text-input :id="$uid . '-prefix'" class="mt-1 block w-full font-mono uppercase" type="text" name="prefix" maxlength="5"
                      :value="$service?->prefix" autocomplete="off" required />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Up to 5 letters or digits. Tickets read like <span class="font-mono">{{ $service?->prefix ?: 'R' }}-001</span>.</p>
    </div>
</x-ajax-form-modal>
