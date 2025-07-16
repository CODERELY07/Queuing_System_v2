
<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Queue Stats -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-4">Registration Queue</h3>
                        <p class="text-3xl font-bold">12</p>
                        <p class="text-gray-500">Waiting patients</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-4">Quick Actions</h3>
                        <button class="bg-green-500 px-4 py-2 rounded-lg w-full mb-2">
                            Call Next Patient
                        </button>
                        <button class="bg-blue-500 px-4 py-2 rounded-lg w-full">
                            Transfer Queue
                        </button>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-4">Recent Activity</h3>
                        <ul class="space-y-2">
                            <li>D-037 - Completed</li>
                            <li>D-038 - Serving</li>
                            <li>D-039 - Waiting</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
