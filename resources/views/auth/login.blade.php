<x-guest-layout :hide-footer="true">
    <x-page-shell>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <x-card>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Log in</h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:focus:ring-offset-gray-800" name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end pt-2">
                    {{-- @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif --}}

                    <x-primary-button class="ms-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        {{-- Portfolio project only: every real account here is seeded demo
             data (database/seeders/UserSeeder.php), not a real clinic, so
             there's nothing sensitive about publishing the credentials —
             this exists purely so anyone looking at the portfolio can try
             every role without digging through the README. Click a row to
             fill and submit the form above in one step. --}}
        <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-2 mb-1">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Demo accounts</h3>
                <span class="text-[11px] font-medium uppercase tracking-wide text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/40 rounded-full px-2 py-0.5">Portfolio demo</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">This is a demo project, not a real clinic — click any account below to log in instantly.</p>

            <div class="space-y-1.5">
                @foreach ([
                    ['role' => 'Admin', 'email' => 'admin@medqueue.test', 'password' => 'admin12345'],
                    ['role' => 'Staff · Registration', 'email' => 'registration@medqueue.test', 'password' => 'staff12345'],
                    ['role' => 'Staff · Doctor Consultation', 'email' => 'doctor@medqueue.test', 'password' => 'staff12345'],
                    ['role' => 'Staff · Pharmacy', 'email' => 'pharmacy@medqueue.test', 'password' => 'staff12345'],
                    ['role' => 'Staff · Emergency', 'email' => 'emergency@medqueue.test', 'password' => 'staff12345'],
                ] as $account)
                    <button
                        type="button"
                        aria-label="{{ __('Log in as :role', ['role' => $account['role']]) }}"
                        onclick="document.getElementById('email').value='{{ $account['email'] }}'; document.getElementById('password').value='{{ $account['password'] }}'; document.getElementById('email').closest('form').submit();"
                        class="w-full flex items-center justify-between gap-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-600 hover:bg-brand-50/50 dark:hover:bg-brand-900/20 transition-colors px-3 py-2 text-left">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $account['role'] }}</span>
                        <span class="text-xs font-mono text-gray-400 dark:text-gray-500 text-right leading-tight">
                            {{ $account['email'] }}<br>{{ $account['password'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </x-page-shell>
</x-guest-layout>
