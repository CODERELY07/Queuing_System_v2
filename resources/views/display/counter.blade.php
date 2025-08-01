<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Counter') }}
        </h2>
    </x-slot>

    <div class="py-12">
     
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden  sm:rounded-xl">
                <div class="p-8 bg-white dark:bg-gray-900">
                    <h3 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white text-center">Currently Serving</h3>

                    <div id="serving-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

