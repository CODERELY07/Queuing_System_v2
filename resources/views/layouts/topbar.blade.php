<div class="flex items-center justify-between gap-3 py-4 h-16 px-4 sm:px-6 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800">

    <div class="flex items-center gap-3 min-w-0">
        <button type="button" @click="sidebarOpen = true"
                class="sm:hidden flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                aria-label="{{ __('Open menu') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <form method="GET" action="{{ url()->current() }}" class="w-full max-w-sm">
            @foreach (request()->except(['q', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <label class="relative block">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Search for anything') }}"
                       class="w-full rounded-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 pl-9 pr-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500" />
            </label>
        </form>
    </div>

    <x-dropdown align="right" width="56">
        <x-slot name="trigger">
            <button class="flex items-center gap-2.5 rounded-full pl-1 pr-3 py-1 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <x-avatar-initials :name="Auth::user()->name" />
                <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
            </div>
            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
