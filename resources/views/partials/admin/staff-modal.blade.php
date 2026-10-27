@php
    // Field ids must be unique on the page: this partial is included once
    // per staff row plus once for "Add", and every copy used to share the
    // same ids (name, email, password…), so a <label for> always focused
    // the first copy's input — usually in a different, hidden dialog.
    $uid = $staff ? "staff-{$staff->id}" : 'staff-new';
@endphp

<x-ajax-form-modal
    :name="$staff ? 'edit-staff-' . $staff->id : 'create-staff'"
    :title="$staff ? 'Edit staff member' : 'Add staff member'"
    :action="$action"
    :method="$method"
    :submit="$staff ? 'Save changes' : 'Add staff member'">

    <div>
        <x-input-label :for="$uid . '-name'" value="Name" />
        <x-text-input :id="$uid . '-name'" class="mt-1 block w-full" type="text" name="name" maxlength="50"
                      :value="$staff?->name" autocomplete="off" required />
    </div>

    <div>
        <x-input-label :for="$uid . '-email'" value="Email" />
        <x-text-input :id="$uid . '-email'" class="mt-1 block w-full" type="email" name="email" maxlength="255"
                      :value="$staff?->email" autocomplete="off" required />
    </div>

    <div>
        <x-input-label :for="$uid . '-service'" value="Department" />
        <select id="{{ $uid }}-service" name="service_id" class="mt-1 block w-full" required>
            <option value="" disabled @selected(!$staff)>Choose a department…</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}" @selected($staff?->service_id === $service->id)>{{ $service->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- new-password, not off: browsers ignore autocomplete="off" on
         password fields and would otherwise fill the *admin's own* saved
         login into the staff member's password. --}}
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <x-input-label :for="$uid . '-password'" value="Password" />
            <x-text-input :id="$uid . '-password'" class="mt-1 block w-full" type="password" name="password"
                          autocomplete="new-password" :required="!$staff" />
        </div>
        <div>
            <x-input-label :for="$uid . '-password-confirmation'" value="Confirm password" />
            <x-text-input :id="$uid . '-password-confirmation'" class="mt-1 block w-full" type="password" name="password_confirmation"
                          autocomplete="new-password" :required="!$staff" />
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 sm:col-span-2">
            {{ $staff ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}
        </p>
    </div>
</x-ajax-form-modal>
