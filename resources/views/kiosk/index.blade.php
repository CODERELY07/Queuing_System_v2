<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kiosk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
             <div class="max-w-md mx-auto bg-white rounded-xl shadow-md p-8">
                <h1 class="text-2xl font-bold text-center mb-6">Select Service</h1>
                <div class="space-y-4">
                    <button class="w-full bg-blue-500 hover:bg-blue-600 py-3 px-4 rounded-lg">
                        Registration
                    </button>
                    <button class="w-full bg-green-500 hover:bg-green-600 py-3 px-4 rounded-lg">
                        Doctor Consultation
                    </button>
                    <button class="w-full bg-yellow-500 hover:bg-yellow-600 py-3 px-4 rounded-lg">
                        Pharmacy
                    </button>
                    <button class="w-full bg-red-500 hover:bg-red-600 py-3 px-4 rounded-lg">
                        Emergency
                    </button>
                </div>
            </div>
            </div>
        </div>
    </div>
</x-guest-layout>
