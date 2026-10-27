<x-guest-layout title="Get a queue number" :hide-footer="true">
    <x-page-shell max-width="max-w-xl">
        <div class="mb-8 text-center">
            <x-eyebrow-badge>Patient intake</x-eyebrow-badge>
            <h1 class="mt-3 font-display text-4xl font-medium tracking-tight text-gray-900 dark:text-white">Get your queue number</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Tell us who you are and where you're headed.</p>
        </div>

        <x-card>
            @if ($services->isEmpty())
                <p class="py-6 text-center text-gray-500 dark:text-gray-400">No services are open right now. Please ask the front desk for help.</p>
            @else
                {{-- `sending` locks the form after the first press: on a
                     touchscreen a double-tap is easy, and each submit
                     issues a ticket — two taps meant two tickets. --}}
                <form method="POST" action="{{ route('kiosk.store') }}" class="space-y-7" x-data="{ sending: false }" @submit="sending = true">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Your name" />
                        <x-text-input type="text" name="name" id="name" value="{{ old('name') }}"
                                      class="mt-2 block h-12 w-full text-base" placeholder="Full name"
                                      maxlength="100" autocomplete="off" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <fieldset>
                        <legend class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Service</legend>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($services as $service)
                                <label class="relative flex min-h-[4.5rem] cursor-pointer items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 transition-colors hover:border-gray-300 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:hover:border-gray-600 dark:has-[:checked]:border-brand-400 dark:has-[:checked]:bg-brand-900/30">
                                    <input type="radio" name="service_id" value="{{ $service->id }}" class="sr-only" required @checked((int) old('service_id') === $service->id) />
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 font-mono text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $service->prefix }}</span>
                                    <span class="font-medium leading-snug text-gray-900 dark:text-white">{{ $service->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                    </fieldset>

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition-colors hover:border-gray-300 has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 dark:border-gray-700 dark:hover:border-gray-600 dark:has-[:checked]:border-violet-500 dark:has-[:checked]:bg-violet-900/20">
                        <input type="checkbox" name="priority" value="1" class="mt-0.5 h-5 w-5 rounded text-violet-600 focus:ring-violet-500" @checked(old('priority')) />
                        <span>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Priority lane</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">Senior citizen, PWD, or pregnant — called ahead of the regular line.</span>
                        </span>
                    </label>

                    <x-cta-button type="submit" size="lg" class="w-full" ::disabled="sending">
                        <span x-show="!sending">Get queue number</span>
                        <span x-show="sending" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                            Getting your number…
                        </span>
                    </x-cta-button>
                </form>
            @endif
        </x-card>
    </x-page-shell>
</x-guest-layout>
