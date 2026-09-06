<x-app-layout>
    <x-page-header title="Service Management" />

    <x-panel class="overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $services->count() }} service{{ $services->count() === 1 ? '' : 's' }}</p>
            <x-primary-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'create-service')">
                {{ __('Add New Service') }}
            </x-primary-button>
        </div>

        <div class="overflow-x-auto">
            @include('partials.admin.service-table', ['services' => $services])
        </div>
    </x-panel>

    @include('partials.admin.service-modal', [
        'key' => 'Add New Service',
        'action' => route('services.store'),
        'method' => 'POST',
        'service' => null,
    ])
</x-app-layout>
