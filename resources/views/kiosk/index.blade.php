<x-guest-layout :hide-footer="true">
    <x-page-shell max-width="max-w-lg">
        <div class="text-center mb-6">
            <x-eyebrow-badge class="mb-3">
                <x-application-logo class="h-3.5 w-3.5" />
                Patient Intake
            </x-eyebrow-badge>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Queue Registration</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Fill in your details to get your queue number.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('kiosk.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input type="text" name="name" id="name" value="{{ old('name') }}"
                                  class="mt-1 block w-full" placeholder="Your full name" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Service</legend>
                    <div class="grid grid-cols-2 gap-3" role="radiogroup">
                        @foreach($services as $service)
                            @if ($service->name === "Admin")
                                @continue;
                            @endif
                            <label class="relative flex min-h-[96px] flex-col items-center justify-center gap-1 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 text-center cursor-pointer transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:dark:bg-brand-900/30 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-500">
                                <input type="radio" name="service_id" value="{{ $service->id }}" class="sr-only" required
                                       @checked(old('service_id') == $service->id) />
                                <span class="font-semibold text-gray-900 dark:text-white leading-snug">{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                </fieldset>

                <label class="flex items-start gap-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 has-[:checked]:dark:bg-violet-900/20 p-4 cursor-pointer transition">
                    <input type="checkbox" name="priority" value="1" class="mt-0.5 h-5 w-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500" @checked(old('priority')) />
                    <span>
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">Priority lane</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Senior citizen, PWD, or pregnant — called ahead of the regular line.</span>
                    </span>
                </label>

                <x-cta-button type="submit" size="lg" class="w-full">
                    Get Queue Number
                </x-cta-button>
            </form>
        </x-card>
    </x-page-shell>
</x-guest-layout>
