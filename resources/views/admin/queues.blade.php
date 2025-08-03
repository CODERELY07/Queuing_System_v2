<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin - Queues') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('include.message')
                <form action="{{ route('queues.deleteOld') }}" method="POST" onsubmit="return confirm('Delete all queues from previous days?');" class="h-[50px] relative">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mb-4 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded absolute text-sm right-0">
                        Delete Old Queues
                    </button>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
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
                                        <form action="{{ route('queues.destroy', $queue->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded">
                                                Delete
                                            </button>
                                        </form>
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
                </div>

                <div class="mt-6">
                    {{ $queues->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
