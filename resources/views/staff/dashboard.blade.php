
<x-app-layout>
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
                        <h3 class="font-bold text-lg mb-4">{{ $user->service->name}} Queue</h3>
                          <p id="waiting-count" class="text-3xl font-bold">--</p>
                          <p class="text-gray-500">Waiting patients</p>
                    </div>  

                    <!-- Quick Actions -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-4">Quick Actions</h3>
                        <h2 id="serving-patient-number"  class="text-3xl font-bold text-center"></h2>
                        <h2 id="serving-patient-name" class="text-lg p-2 mb-4 text-center"></h2>
                        
                         <button id="call-prev-btn" class="bg-green-500 px-4 py-2 rounded-lg w-full mb-2">
                            Call Prev Patient
                        </button>
                         <button id="call-next-btn" class="bg-blue-500 px-4 py-2 rounded-lg w-full mb-2">
                            Call Next Patient
                        </button>
                    </div>

                   <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-4">Recent Activity</h3>
                        <ul id="recent-activity" class="space-y-2">
                            <li>Loading...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
