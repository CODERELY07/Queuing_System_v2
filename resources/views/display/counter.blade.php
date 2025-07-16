
<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Counter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-blue-800 min-h-screen p-8">
                    <h1 class="text-4xl font-bold text-center mb-12">Doctor Consultation</h1>
                    
                    <div class="bg-white text-blue-900 rounded-xl p-6 max-w-2xl mx-auto">
                        <div class="text-center mb-8">
                            <p class="text-lg">Now Serving</p>
                            <p class="text-6xl font-bold my-4">D-038</p>
                            <p class="text-xl">Room 3 - Dr. Smith</p>
                        </div>

                        <div class="border-t-2 border-blue-200 pt-6">
                            <h3 class="text-xl font-semibold mb-4">Up Next:</h3>
                            <ul class="space-y-3">
                                <li class="text-2xl">D-039</li>
                                <li class="text-2xl">D-040</li>
                                <li class="text-2xl">D-041</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
