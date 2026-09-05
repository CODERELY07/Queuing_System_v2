<header x-data="{ open: false }" class="bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-gray-100 dark:border-gray-800 sticky top-0 z-30">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <!-- Logo/Brand -->
      <a class="flex items-center gap-2 shrink-0" href="{{ route('home') }}">
        <x-application-logo class="h-7 w-7 text-brand-600 dark:text-brand-400" />
        <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">MedQueue</span>
      </a>

      <!-- Main Navigation -->
      <nav class="hidden md:block">
        <ul class="flex items-center gap-1">
          @php $navItem = 'px-3 py-2 rounded-md text-sm font-medium transition'; @endphp
          <li>
              <a href="{{ route('kiosk') }}"
                 class="{{ $navItem }} {{ request()->routeIs('kiosk') ? 'text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-900/40' : 'text-gray-600 dark:text-gray-300 hover:text-brand-700 dark:hover:text-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                  Kiosk
              </a>
          </li>
          <li>
              <a href="{{ route('display') }}"
                 class="{{ $navItem }} {{ request()->routeIs('display') ? 'text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-900/40' : 'text-gray-600 dark:text-gray-300 hover:text-brand-700 dark:hover:text-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                  Queue Display
              </a>
          </li>
          <li>
              <a href="{{ route('login') }}" class="{{ $navItem }} text-gray-600 dark:text-gray-300 hover:text-brand-700 dark:hover:text-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                  Staff Login
              </a>
          </li>
        </ul>
      </nav>

      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <button @click="open = ! open" aria-label="Toggle navigation" :aria-expanded="open"
                class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition.origin.top x-cloak class="md:hidden pb-4">
      <ul class="space-y-1">
        <li><a href="{{ route('kiosk') }}" class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-brand-50 dark:hover:bg-brand-900/40 hover:text-brand-700 dark:hover:text-brand-300">Kiosk</a></li>
        <li><a href="{{ route('display') }}" class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-brand-50 dark:hover:bg-brand-900/40 hover:text-brand-700 dark:hover:text-brand-300">Queue Display</a></li>
        <li><a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-brand-50 dark:hover:bg-brand-900/40 hover:text-brand-700 dark:hover:text-brand-300">Staff Login</a></li>
      </ul>
    </div>
  </div>
</header>
