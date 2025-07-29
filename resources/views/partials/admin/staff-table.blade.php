<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Name
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Email
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Role
            </th>
            {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th> --}}
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>           
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <!-- Sample Data Row -->
        @foreach ($staffs as $staff)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $staff->name }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                    {{ $staff->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                    {{$staff->service->name }}
                </td>
              
                
                {{-- <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        {{ $isActive }}
                    </span>
                </td> --}}
                <td class="px-6 flex justify-end items-center py-4 whitespace-nowrap text-right text-sm font-medium">
                  <x-primary-button 
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'edit-staff-{{ $staff->id }}')"
                    class="text-blue-600 bg-blue-500 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                    {{ __('Edit') }}
                </x-primary-button>
                   <form data-action="{{ route('staff.destroy', $staff->id) }}" method="POST" id="staff-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                        Delete
                    </button>
                </form>

                </td>

                {{-- Edit modal --}}
                @include('partials.admin.staff-modal', [
                    'key' => 'Edit Staff',
                    'action' => route('staff.update', $staff->id),
                    'method' => 'PUT',
                    'staff' => $staff,
                ])

            </tr>
        @endforeach
    </tbody>
</table>
