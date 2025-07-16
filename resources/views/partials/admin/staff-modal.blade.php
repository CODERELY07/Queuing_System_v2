<x-modal name="{{ $staff ? 'edit-staff-' . $staff->id : 'create-staff' }}" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            {{ __($staff ? 'Edit Staff' : 'Add New Staff') }}
        </h2>

        <div id="success-message" class="mt-4 hidden"></div>
        
        <form method="POST" id="staffForm" data-action="{{ $action }}">
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
                        :value="old('name', $staff?->name)"
                        
                    />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full"
                        type="email"
                        name="email"
                        :value="old('email', $staff?->email)"
                        
                    />
                </div>
            
                <div>
                    <x-input-label for="service" :value="__('Service')" />
                    <select
                        id="service_id"
                        name="service_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        
                    >
                        <option value=""></option>
                        @foreach ($services as $service)  
                            <option value={{ $service->id }}>{{ $service->name}}</option>
                        @endforeach
                    </select>
                </div>
                
                @if(!$staff)
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"  />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                    </div>
                @endif
            </div>

            <div class="flex justify-end mt-6">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __($staff ? 'Update' : 'Add') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
