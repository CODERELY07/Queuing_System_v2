<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Counter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-900">
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Currently Serving</h3>

                    <div id="serving-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
