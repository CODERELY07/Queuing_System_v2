<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kiosk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto flex justify-center items-center flex-col text-center">
            <div>
                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mx-auto rounded-xl shadow-md sm:w-[400px] w-[300px] p-8">
                    <h1 class="text-2xl font-bold text-center mb-6">Queue Registration</h1>

                    <form method="POST" action="{{ route('kiosk.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                   >
                        </div>


                        <div class="mb-4">
                            <label for="service_id" class="block text-sm font-medium text-gray-700">Select Service</label>
                            <select name="service_id" id="service_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                    >
                                <option value="">-- Choose a Service --</option>
                                @foreach($services as $service)
                                    @if ($service->name === "Admin")
                                        @continue;
                                    @endif
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg">
                                Get Queue Number
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
