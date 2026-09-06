@php
    // One class string builder for every nav row — active state is a soft
    // brand-tinted pill (same brand-50/600 pairing as the rest of the admin
    // UI's tone system), inactive is muted gray that darkens on hover.
    $itemClass = fn (bool $active) => 'group relative flex items-center gap-3 h-11 px-3 rounded-xl transition-colors duration-200 '
        . ($active
            ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400'
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800');

    // A label next to an icon fades and shrinks away when the rail
    // collapses, rather than just vanishing — `shrink-0` on the icon next
    // to it keeps the icon from being squeezed as the label's width tweens.
    // The actual show/hide is a plain CSS class (.nav-label, in app.css)
    // driven by .is-collapsed on the <aside>, not a per-element Alpine
    // binding — see app.css for why.
    $labelClass = 'nav-label text-sm font-medium whitespace-nowrap overflow-hidden';
@endphp

{{--
    Persistent left nav — icon + label when expanded, a narrow icon-only
    rail when collapsed. The collapsed/expanded state lives in localStorage
    (read in layouts.app's root x-data) so it survives a full page reload,
    since this is server-rendered navigation, not an SPA.

    Light chrome that follows the app's own light/dark mode now, matching
    the rest of the admin UI instead of standing apart from it as an
    always-dark rail.

    Below `sm` it's an off-canvas drawer (fixed, slides in from the left,
    toggled by the hamburger in the topbar) at full width regardless of the
    desktop collapsed state — see the .sidebar-drawer override in app.css.
--}}
<aside
    :class="{ 'sidebar-open': sidebarOpen, 'is-collapsed': sidebarCollapsed }"
    x-bind:style="{ width: sidebarCollapsed ? '5rem' : '16rem' }"
    style="width: 16rem"
    class="sidebar-drawer fixed sm:static inset-y-0 left-0 z-40 flex flex-col shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 py-6 overflow-hidden transition-[width] duration-300 ease-in-out">

    <div class="flex items-center gap-2 px-4 mb-6">
        <a href="{{ route('dashboard', ['user_type' => Auth::user()->user_type]) }}"
           class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-50 dark:bg-brand-500/10 ring-1 ring-inset ring-brand-200 dark:ring-brand-500/30 text-brand-600 dark:text-brand-400"
           aria-label="{{ __('Dashboard home') }}">
            <x-application-logo class="h-6 w-6" />
        </a>

        <span class="nav-label text-base font-bold text-gray-900 dark:text-white whitespace-nowrap overflow-hidden">
            MedQueue
        </span>

        <button type="button" @click="sidebarOpen = false"
                class="sm:hidden ml-auto flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                aria-label="{{ __('Close menu') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Desktop-only: collapsing to an icon rail doesn't make sense on the
         mobile off-canvas drawer, which is already an overlay the
         hamburger/backdrop can dismiss. Sits right under the brand — same
         spot this control lives in most collapsible sidebars — rather
         than at the very bottom grouped in with unrelated account actions
         (Profile, Log out) like it did before.

         Same plain 3-line icon as the mobile menu button above, and it
         stays that one icon in both states — it used to be a
         double-chevron that flipped into its mirror image on toggle,
         which read as two different, inconsistent icons rather than one
         control with two states. --}}
    <div class="hidden sm:block px-3 pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
        <button type="button" @click="toggleSidebar()"
                class="flex items-center gap-3 h-11 px-3 w-full rounded-xl text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
                :aria-label="sidebarCollapsed ? '{{ __('Expand sidebar') }}' : '{{ __('Collapse sidebar') }}'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span class="{{ $labelClass }}" x-text="sidebarCollapsed ? '{{ __('Expand') }}' : '{{ __('Collapse') }}'"></span>
        </button>
    </div>

    <nav class="flex-1 flex flex-col gap-1 px-3 overflow-y-auto overflow-x-hidden">
        <a href="{{ route('dashboard', ['user_type' => Auth::user()->user_type]) }}"
           class="{{ $itemClass(request()->routeIs('dashboard*')) }}"
           title="{{ __('Dashboard') }}" aria-current="{{ request()->routeIs('dashboard*') ? 'page' : 'false' }}">
            <x-icon.dashboard class="h-5 w-5 shrink-0" />
            <span class="{{ $labelClass }}">{{ __('Dashboard') }}</span>
        </a>

        @if (Auth::user()->user_type === 'admin')
            <a href="{{ route('admin.queues') }}"
               class="{{ $itemClass(request()->routeIs('admin.queues')) }}"
               title="{{ __('Queues') }}" aria-current="{{ request()->routeIs('admin.queues') ? 'page' : 'false' }}">
                <x-icon.clipboard class="h-5 w-5 shrink-0" />
                <span class="{{ $labelClass }}">{{ __('Queues') }}</span>
            </a>
            <a href="{{ route('admin.staff') }}"
               class="{{ $itemClass(request()->routeIs('admin.staff')) }}"
               title="{{ __('Staff') }}" aria-current="{{ request()->routeIs('admin.staff') ? 'page' : 'false' }}">
                <x-icon.staff class="h-5 w-5 shrink-0" />
                <span class="{{ $labelClass }}">{{ __('Staff') }}</span>
            </a>
            <a href="{{ route('admin.services') }}"
               class="{{ $itemClass(request()->routeIs('admin.services')) }}"
               title="{{ __('Services') }}" aria-current="{{ request()->routeIs('admin.services') ? 'page' : 'false' }}">
                <x-icon.layers class="h-5 w-5 shrink-0" />
                <span class="{{ $labelClass }}">{{ __('Services') }}</span>
            </a>
        @endif
    </nav>

    <div class="flex flex-col gap-1 px-3 pt-3 mt-3 border-t border-gray-100 dark:border-gray-800">
        <a href="{{ route('profile.edit') }}"
           class="{{ $itemClass(request()->routeIs('profile.edit')) }}"
           title="{{ __('Profile') }}" aria-current="{{ request()->routeIs('profile.edit') ? 'page' : 'false' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="{{ $labelClass }}">{{ __('Profile') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group flex items-center gap-3 h-11 px-3 w-full rounded-xl text-gray-500 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors duration-200"
                    title="{{ __('Log out') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="{{ $labelClass }}">{{ __('Log out') }}</span>
            </button>
        </form>
    </div>
</aside>
