{{-- `$search` comes from <x-app-layout :search="true"> — only the pages that
     actually read `?q=` (the queue tables, the staff list) turn it on. --}}
<div class="sticky top-0 z-20 flex h-14 items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900 sm:px-6 lg:px-8">

    <div class="flex min-w-0 flex-1 items-center gap-3">
        <button type="button" @click="sidebarOpen = true"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 sm:hidden"
                aria-label="{{ __('Open menu') }}">
            <x-icon.menu class="h-5 w-5" />
        </button>

        @if ($search)
            {{-- Pressing "/" anywhere on the page jumps to the box (unless
                 you're already typing in a field). The clear button is a
                 plain link back to the same page minus `q`, so it works
                 without JS and keeps sort/filter params. --}}
            <form method="GET" action="{{ url()->current() }}" role="search"
                  x-data="{ focused: false }"
                  @keydown.window.slash="if (!['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) { $event.preventDefault(); $refs.q.focus(); }"
                  class="w-full max-w-2xl">
                @foreach (request()->except(['q', 'page']) as $key => $value)
                    @if (is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label class="group relative block">
                    <span class="sr-only">{{ __('Search') }}</span>
                    <x-icon.search class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-brand-600" />
                    <input type="search" name="q" x-ref="q" value="{{ request('q') }}" maxlength="100"
                           @focus="focused = true" @blur="focused = false"
                           placeholder="{{ __('Search by name or ticket number…') }}"
                           class="h-10 w-full rounded-full border border-gray-200 bg-gray-50 pl-11 pr-20 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-brand-500 focus:bg-white focus:shadow-md focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:bg-gray-900 [&::-webkit-search-cancel-button]:appearance-none" />

                    <span class="absolute right-3 top-1/2 flex -translate-y-1/2 items-center gap-1.5">
                        @if (request()->filled('q'))
                            <a href="{{ url()->current() . (($rest = http_build_query(request()->except(['q', 'page']))) ? '?' . $rest : '') }}"
                               class="flex h-6 w-6 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-200 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                               aria-label="{{ __('Clear search') }}">
                                <x-icon.x class="h-3.5 w-3.5" />
                            </a>
                        @else
                            <kbd x-show="!focused" class="pointer-events-none hidden rounded-md border border-gray-200 bg-white px-1.5 py-0.5 font-mono text-[11px] font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-900 sm:block" aria-hidden="true">/</kbd>
                        @endif
                    </span>
                </label>
            </form>
        @endif
    </div>

    <x-dropdown align="right" width="56">
        <x-slot name="trigger">
            <button type="button" class="flex items-center gap-2.5 rounded-lg py-1 pl-1 pr-2 transition hover:bg-gray-100 dark:hover:bg-gray-800" aria-haspopup="menu">
                <x-avatar-initials :name="Auth::user()->name" size="sm" />
                <span class="hidden text-sm font-medium text-gray-700 dark:text-gray-200 sm:block">{{ Auth::user()->name }}</span>
                <x-icon.chevron-down class="h-4 w-4 text-gray-400" />
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                <p class="mt-1.5 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium capitalize text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ Auth::user()->user_type }}</p>
            </div>
            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
