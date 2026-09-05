<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-brand-50 dark:bg-brand-900/40 text-brand-600 dark:text-brand-400 mb-4">
                    <x-icon.check-circle class="h-6 w-6" />
                </div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Welcome back, {{ Auth::user()->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Jump into your dashboard, or manage staff and queues below.</p>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    <x-cta-button :href="route('dashboard', ['user_type' => 'admin'])">
                        Go to Dashboard
                    </x-cta-button>
                    <x-cta-button :href="route('admin.staff')" variant="outline">
                        Manage Staff
                    </x-cta-button>
                    <x-cta-button :href="route('admin.queues')" variant="outline">
                        Manage Queues
                    </x-cta-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
