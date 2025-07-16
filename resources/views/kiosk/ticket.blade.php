
<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kiosk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="max-w-sm mx-auto bg-white rounded-xl shadow-lg p-6 text-center">
                <h2 class="text-xl font-semibold mb-2">Your Queue Number</h2>
                <div class="text-5xl font-bold py-6 bg-gray-100 rounded-lg mb-4">
                    D-042
                </div>
                <p class="text-gray-600 mb-4">Estimated wait time: <span class="font-bold">15 minutes</span></p>
                <div class="border-t pt-4">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                        Send to Phone
                    </button>
                </div>
            </div>
            </div>
        </div>
    </div>
</x-guest-layout>
