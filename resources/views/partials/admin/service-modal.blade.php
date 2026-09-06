<x-modal name="{{ $service ? 'edit-service-' . $service->id : 'create-service' }}" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            {{ __($service ? 'Edit Service' : 'Add New Service') }}
        </h2>

        <form method="POST" id="serviceForm-{{ $service?->id ?? 'new' }}" data-action="{{ $action }}">
            @csrf
            @if($method === 'PUT')
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-6 mt-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input
                        id="name"
                        class="block mt-1 w-full"
                        type="text"
                        name="name"
                        :value="old('name', $service?->name)"
                    />
                </div>

                <div>
                    <x-input-label for="prefix" :value="__('Ticket Prefix')" />
                    <x-text-input
                        id="prefix"
                        class="block mt-1 w-full uppercase"
                        type="text"
                        name="prefix"
                        maxlength="5"
                        :value="old('prefix', $service?->prefix)"
                    />
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Shown on tickets, e.g. "{{ old('prefix', $service?->prefix) ?: 'R' }}-001".</p>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __($service ? 'Update' : 'Add') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
