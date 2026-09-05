@php
    // One class string builder for every icon link below — active state is
    // a soft white pill, inactive is muted gray that brightens on hover.
    $itemClass = fn (bool $active) => 'group flex h-11 w-11 items-center justify-center rounded-xl transition '
        . ($active ? 'bg-white/10 text-white' : 'text-gray-500 hover:text-gray-200 hover:bg-white/5');
@endphp

{{--
    Persistent left icon-rail, styled after the reference dashboard: dark
    chrome regardless of the page's own light/dark mode (nav chrome, not
    body content — same reasoning as the always-dark public display board),
    icon-only links with a soft pill behind the active one.

    Below `sm` it's an off-canvas drawer (fixed, slides in from the left,
    toggled by the hamburger in the topbar) rather than simply hidden —
    there was previously no way to reach staff/admin nav on a phone at all.
--}}
<aside
    :class="{ 'sidebar-open': sidebarOpen }"
    class="sidebar-drawer fixed sm:static inset-y-0 left-0 z-40 flex flex-col items-center w-20 shrink-0 bg-gray-950 py-6 gap-2">
    <button type="button" @click="sidebarOpen = false"
            class="sm:hidden flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 hover:text-white hover:bg-white/5 transition mb-2"
            aria-label="{{ __('Close menu') }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <a href="{{ route('dashboard', ['user_type' => Auth::user()->user_type]) }}"
       class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500/15 ring-1 ring-inset ring-brand-400/30 text-brand-400 mb-6"
       aria-label="{{ __('Dashboard home') }}">
        <x-application-logo class="h-6 w-6" />
    </a>

    <nav class="flex flex-col items-center gap-1.5">
        <a href="{{ route('dashboard', ['user_type' => Auth::user()->user_type]) }}"
           class="{{ $itemClass(request()->routeIs('dashboard*')) }}"
           title="{{ __('Dashboard') }}" aria-current="{{ request()->routeIs('dashboard*') ? 'page' : 'false' }}">
            <x-icon.dashboard class="h-5 w-5" />
        </a>

        @if (Auth::user()->user_type === 'admin')
            <a href="{{ route('admin.queues') }}"
               class="{{ $itemClass(request()->routeIs('admin.queues')) }}"
               title="{{ __('Queues') }}" aria-current="{{ request()->routeIs('admin.queues') ? 'page' : 'false' }}">
                <x-icon.clipboard class="h-5 w-5" />
            </a>
            <a href="{{ route('admin.staff') }}"
               class="{{ $itemClass(request()->routeIs('admin.staff')) }}"
               title="{{ __('Staff') }}" aria-current="{{ request()->routeIs('admin.staff') ? 'page' : 'false' }}">
                <x-icon.staff class="h-5 w-5" />
            </a>
        @endif
    </nav>

    <div class="mt-auto flex flex-col items-center gap-1.5">
        <a href="{{ route('profile.edit') }}"
           class="{{ $itemClass(request()->routeIs('profile.edit')) }}"
           title="{{ __('Profile') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="group flex h-11 w-11 items-center justify-center rounded-xl text-gray-500 hover:text-rose-400 hover:bg-white/5 transition" title="{{ __('Log out') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
</aside>
