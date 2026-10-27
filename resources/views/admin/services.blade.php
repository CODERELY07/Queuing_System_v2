<x-app-layout title="Services">
    <x-page-header title="Services" subtitle="The departments visitors can take a ticket for." />

    <x-panel class="overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 p-4 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <span class="font-mono tabular-nums">{{ $services->count() }}</span> {{ Str::plural('service', $services->count()) }}
            </p>
            <x-primary-button
                size="sm"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'create-service')">
                <x-icon.plus class="h-4 w-4" />
                {{ __('Add service') }}
            </x-primary-button>
        </div>

        <div class="overflow-x-auto">
            @include('partials.admin.service-table', ['services' => $services])
        </div>
    </x-panel>

    @include('partials.admin.service-modal', [
        'action' => route('services.store'),
        'method' => 'POST',
        'service' => null,
    ])
</x-app-layout>
