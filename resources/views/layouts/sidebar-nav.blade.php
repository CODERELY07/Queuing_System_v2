@php
    $user = Auth::user();
    $isAdmin = $user->user_type === 'admin';

    // One definition of the nav, rendered by the loop below. `section`
    // groups rows under a small label; `match` is the route pattern that
    // lights the row up as the current page.
    $nav = collect([
        ['label' => __('Dashboard'), 'icon' => 'grid', 'href' => route('dashboard.with_type', ['user_type' => $user->user_type]), 'match' => 'dashboard*', 'section' => null, 'show' => true],
        ['label' => __('Queues'), 'icon' => 'clipboard', 'href' => route('admin.queues'), 'match' => 'admin.queues*', 'section' => __('Manage'), 'show' => $isAdmin],
        ['label' => __('Staff'), 'icon' => 'staff', 'href' => route('admin.staff'), 'match' => 'admin.staff', 'section' => __('Manage'), 'show' => $isAdmin],
        ['label' => __('Services'), 'icon' => 'layers', 'href' => route('admin.services'), 'match' => 'admin.services', 'section' => __('Manage'), 'show' => $isAdmin],
    ])->where('show', true)->groupBy(fn ($item) => $item['section'] ?? '');

    // Active row: a quiet neutral fill plus a 2px brand-colored tick on the
    // leading edge and a brand-colored icon — enough to say "you are here"
    // without a loud filled pill. Inactive rows are muted and lift on hover.
    $itemClass = fn (bool $active) => 'group relative flex h-10 items-center gap-3 rounded-lg px-3 text-[13.5px] font-medium transition-colors duration-150 '
        . ($active
            ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white before:absolute before:inset-y-2 before:left-0 before:w-0.5 before:rounded-full before:bg-brand-600 dark:before:bg-brand-400 [&>svg]:text-brand-600 dark:[&>svg]:text-brand-400'
            : 'text-gray-600 hover:bg-gray-100/70 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800/70 dark:hover:text-gray-100');

    // The actual show/hide for labels is a plain CSS class (.nav-label, in
    // app.css) driven by .is-collapsed on the <aside>, not a per-element
    // Alpine binding — see app.css for why.
    $labelClass = 'nav-label whitespace-nowrap overflow-hidden';
@endphp

{{--
    Persistent left nav — icon + label when expanded, a narrow icon-only
    rail when collapsed. The collapsed/expanded state lives in localStorage
    (read in layouts.app's root x-data) so it survives a full page reload,
    since this is server-rendered navigation, not an SPA.

    Below `sm` it's an off-canvas drawer (fixed, slides in from the left,
    toggled by the hamburger in the topbar) at full width regardless of the
    desktop collapsed state — see the .sidebar-drawer override in app.css.
--}}
<aside
    :class="{ 'sidebar-open': sidebarOpen, 'is-collapsed': sidebarCollapsed }"
    x-bind:style="{ width: sidebarCollapsed ? '4.75rem' : '15rem' }"
    style="width: 15rem"
    aria-label="{{ __('Primary') }}"
    class="sidebar-drawer fixed inset-y-0 left-0 z-40 flex shrink-0 flex-col overflow-hidden border-r border-gray-200 bg-white py-5 transition-[width] duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900 sm:static">

    <div class="mb-5 flex items-center gap-2.5 px-4">
        <a href="{{ route('dashboard.with_type', ['user_type' => $user->user_type]) }}"
           class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-white dark:bg-brand-600"
           aria-label="{{ __('Dashboard home') }}">
            <x-application-logo class="h-5 w-5 text-white" />
        </a>

        <span class="nav-label overflow-hidden whitespace-nowrap text-[15px] font-semibold tracking-tight text-gray-900 dark:text-white">
            MedQueue
        </span>

        <button type="button" @click="sidebarOpen = false"
                class="ml-auto flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-200 sm:hidden"
                aria-label="{{ __('Close menu') }}">
            <x-icon.x class="h-5 w-5" />
        </button>
    </div>

    {{-- Desktop-only: collapsing to an icon rail doesn't make sense on the
         mobile off-canvas drawer, which is already an overlay the
         hamburger/backdrop can dismiss. One plain 3-line icon in both
         states — it's one control with two states, not two icons. --}}
    <div class="mb-3 hidden border-b border-gray-100 px-3 pb-3 dark:border-gray-800 sm:block">
        <button type="button" @click="toggleSidebar()"
                class="flex h-10 w-full items-center gap-3 rounded-lg px-3 text-[13.5px] font-medium text-gray-500 transition-colors duration-150 hover:bg-gray-100/70 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
                :aria-label="sidebarCollapsed ? '{{ __('Expand sidebar') }}' : '{{ __('Collapse sidebar') }}'"
                :aria-expanded="(!sidebarCollapsed).toString()">
            <x-icon.menu class="h-5 w-5 shrink-0" />
            <span class="{{ $labelClass }}" x-text="sidebarCollapsed ? '{{ __('Expand') }}' : '{{ __('Collapse') }}'"></span>
        </button>
    </div>

    <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto overflow-x-hidden px-3">
        @foreach ($nav as $section => $items)
            @if ($section !== '')
                <p class="nav-label mb-1 mt-4 whitespace-nowrap overflow-hidden px-3 text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $section }}</p>
            @endif

            @foreach ($items as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ $item['href'] }}"
                   class="{{ $itemClass($active) }}"
                   title="{{ $item['label'] }}"
                   @if ($active) aria-current="page" @endif>
                    <x-dynamic-component :component="'icon.' . $item['icon']" class="h-[18px] w-[18px] shrink-0" />
                    <span class="{{ $labelClass }}">{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="mt-3 flex flex-col gap-0.5 border-t border-gray-100 px-3 pt-3 dark:border-gray-800">
        <a href="{{ route('profile.edit') }}"
           class="{{ $itemClass(request()->routeIs('profile.edit')) }}"
           title="{{ __('Profile') }}" @if (request()->routeIs('profile.edit')) aria-current="page" @endif>
            <x-icon.user class="h-[18px] w-[18px] shrink-0" />
            <span class="{{ $labelClass }}">{{ __('Profile') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group flex h-10 w-full items-center gap-3 rounded-lg px-3 text-[13.5px] font-medium text-gray-600 transition-colors duration-150 hover:bg-rose-50 hover:text-rose-700 dark:text-gray-400 dark:hover:bg-rose-500/10 dark:hover:text-rose-400"
                    title="{{ __('Log out') }}">
                <x-icon.logout class="h-[18px] w-[18px] shrink-0" />
                <span class="{{ $labelClass }}">{{ __('Log out') }}</span>
            </button>
        </form>
    </div>
</aside>
