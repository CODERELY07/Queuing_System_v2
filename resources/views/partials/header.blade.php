<header class="bg-white shadow-md">
  <div class="container mx-auto px-4">
    <div class="flex justify-between items-center py-4">
      <!-- Logo/Brand -->
      <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <span class="text-xl font-bold text-blue-600">MedQueue</span>
      </div>
      <!-- Main Navigation -->
      <nav class="hidden md:block">
        <ul class="flex space-x-8">
          <li><a href="{{ route('kiosk') }}" class="text-gray-700 hover:text-blue-600 font-medium">Kiosk</a></li>
          <li><a href="{{ route('display') }}" class="text-gray-700 hover:text-blue-600 font-medium">Queue Display</a></li>
          <li><a href="{{ route('staff.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Staff</a></li>
          <li><a href="{{ route('admin') }}" class="text-gray-700 hover:text-blue-600 font-medium">Admin</a></li>
        </ul>
      </nav>

      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu (Hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden pb-4">
      <ul class="space-y-2">
        <li><a href="{{ route('kiosk') }}" class="block px-2 py-1 text-gray-700 hover:bg-blue-50 rounded">Kiosk</a></li>
        <li><a href="{{ route('display') }}" class="block px-2 py-1 text-gray-700 hover:bg-blue-50 rounded">Queue Display</a></li>
        <li><a href="{{ route('staff.dashboard') }}" class="block px-2 py-1 text-gray-700 hover:bg-blue-50 rounded">Staff</a></li>
        <li><a href="{{ route('admin') }}" class="block px-2 py-1 text-gray-700 hover:bg-blue-50 rounded">Admin</a></li>
      </ul>
    </div>
  </div>

  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    });
  </script>
</header>