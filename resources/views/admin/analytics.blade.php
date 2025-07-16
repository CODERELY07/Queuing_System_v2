
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="container mx-auto px-4 py-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Queue Analytics</h1>
                
                <!-- Date Filter -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <h2 class="text-xl font-semibold mb-4 md:mb-0">Performance Metrics</h2>
                        <div class="flex space-x-2">
                            <select class="border-gray-300 rounded-md shadow-sm">
                                <option>Last 7 Days</option>
                                <option>Last 30 Days</option>
                                <option>Last 90 Days</option>
                                <option selected>Custom Range</option>
                            </select>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-md">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Wait Times Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Average Wait Times</h3>
                        <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                            [Chart: Wait Times by Queue Type]
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-gray-500">Registration</p>
                                <p class="font-bold">8.2 min</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Doctor</p>
                                <p class="font-bold">22.5 min</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Pharmacy</p>
                                <p class="font-bold">5.7 min</p>
                            </div>
                        </div>
                    </div>

                    <!-- Volume Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Daily Patient Volume</h3>
                        <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                            [Chart: Patients per Day]
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-gray-500">Today</p>
                                <p class="font-bold">147</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Avg. Daily</p>
                                <p class="font-bold">182</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Queue Performance Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold">Queue Performance</h2>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg. Wait</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg. Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Today</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Efficiency</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">Registration</td>
                                <td class="px-6 py-4 whitespace-nowrap">8.2 min</td>
                                <td class="px-6 py-4 whitespace-nowrap">3.5 min</td>
                                <td class="px-6 py-4 whitespace-nowrap">142</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Good</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">Doctor OPD</td>
                                <td class="px-6 py-4 whitespace-nowrap">22.5 min</td>
                                <td class="px-6 py-4 whitespace-nowrap">12.1 min</td>
                                <td class="px-6 py-4 whitespace-nowrap">87</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Average</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">Pharmacy</td>
                                <td class="px-6 py-4 whitespace-nowrap">5.7 min</td>
                                <td class="px-6 py-4 whitespace-nowrap">2.3 min</td>
                                <td class="px-6 py-4 whitespace-nowrap">115</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Good</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
