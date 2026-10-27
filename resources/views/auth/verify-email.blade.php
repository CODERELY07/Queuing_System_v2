<x-guest-layout title="Verify your email">
    <x-page-shell>
        <x-card>
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 text-sm font-medium text-emerald-700 dark:text-emerald-300">
                    {{ __('A new verification link has been sent to the email address on your account.') }}
                </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div>
                        <x-primary-button>
                            {{ __('Resend Verification Email') }}
                        </x-primary-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="text-sm text-gray-600 underline transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </x-card>
    </x-page-shell>
</x-guest-layout>
