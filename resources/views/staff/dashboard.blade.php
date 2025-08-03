
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
                        
                         <button id="call-prev-btn" class="bg-green-500 px-4 text-gray-200 py-2 rounded-lg w-full mb-2">
                            Prev Patient
                        </button>
                         <button id="call" class="bg-sky-500 text-gray-200 px-4 py-2 rounded-lg w-full mb-2">
                            Call 
                        </button>
                         <button id="call-next-btn" class="bg-blue-500 text-gray-200 px-4 py-2 rounded-lg w-full mb-2">
                            Next Patient
                        </button>
                    </div>

                   <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-lg mb-4">Recent Activity</h3>
                        <ul id="recent-activity" class="space-y-2">
                            <li>Loading...</li>
                        </ul>
                    </div>
                </div>

                <table class="min-w-full divide-y mt-10  divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Queue #</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Name</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Service</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($queues as $queue)
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-200">
                                       {{ $queue->service->prefix }} - {{ str_pad($queue->queue_number, 3, 0, STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-200">{{ $queue->name }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                        {{ $queue->status == 'waiting' ? 'bg-yellow-100 text-yellow-800' :
                                        ($queue->status == 'serving' ? 'bg-blue-100 text-blue-800' :
                                        ($queue->status == 'finish' ? 'bg-green-100 text-green-800' : '')) }}">
                                        {{ ucfirst($queue->status) }}
                                    </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-200">
                                        {{ $queue->service->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">            
                                       <button class="selected-call bg-sky-500 p-1 text-white px-3 rounded shadow-sm" data-id="{{ $queue->id }}" id="selected-call-{{ $queue->id }}">
                                            Call
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center px-4 py-4 text-gray-500 dark:text-gray-400">
                                        No queues found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                </table>
                <div class="mt-6">
                    {{ $queues->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
