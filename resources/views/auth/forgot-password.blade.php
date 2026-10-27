<x-guest-layout title="Forgot password">
    <x-page-shell>
        <x-card>
            <h1 class="mb-4 font-display text-3xl font-medium tracking-tight text-gray-900 dark:text-white">Forgot password</h1>

            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end pt-2">
                    <x-primary-button>
                        {{ __('Email Password Reset Link') }}
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </x-page-shell>
</x-guest-layout>
