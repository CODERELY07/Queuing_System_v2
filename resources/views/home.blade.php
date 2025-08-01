<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('MedQueue') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto flex justify-center items-center flex-col text-center">
            <h1 class="text-4xl font-bold mb-4 text-gray-900 dark:text-white">MedQueue</h1>
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                Welcome to MedQueue — our official medical queueing system. This platform is designed to ensure a smooth, efficient, and organized patient experience. Thank you for trusting our care.
            </p>
            <a href="{{ route('kiosk') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow">
                Go to Kiosk
            </a>
        </div>
    </div>
</x-guest-layout>
