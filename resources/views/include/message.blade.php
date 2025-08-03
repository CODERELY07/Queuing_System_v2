@if (session('error'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)" 
        class="mb-4 px-4 py-2 rounded bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900"
    >
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)" 
        class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900"
    >
        {{ session('success') }}
    </div>
@endif