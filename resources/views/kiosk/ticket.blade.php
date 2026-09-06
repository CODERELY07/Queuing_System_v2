<x-guest-layout :hide-footer="true">
    <x-page-shell>
        <x-card class="text-center">
            <x-eyebrow-badge class="mb-4">
                <x-icon.check class="h-3.5 w-3.5" />
                Ticket confirmed
            </x-eyebrow-badge>

            @if ($queue->priority)
                <div class="mb-2">
                    <x-priority-badge />
                </div>
            @endif

            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Your Queue Number</h2>

            <div id="print-body">
                <div class="font-mono tabular-nums text-7xl sm:text-8xl font-bold py-8 mb-4 rounded-xl bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 tracking-tight animate-flash-once">
                    {{ $queue->formattedNumber() }}
                </div>

                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Estimated wait time: <span class="font-bold text-gray-900 dark:text-gray-100">{{ $estimatedWaitTime }} minutes</span>
                </p>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-5 flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-cta-button id="print">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z" />
                    </svg>
                    Print Ticket
                </x-cta-button>

                <x-cta-button id="back-to-kiosk" :href="route('kiosk')" variant="outline">
                    Back to Kiosk
                </x-cta-button>
            </div>
        </x-card>

        <x-info-callout class="mt-4">
            <span class="block font-semibold text-gray-900 dark:text-white">No more guessing.</span>
            Watch the waiting-room display or listen for your number — we'll call you when it's your turn.
        </x-info-callout>

        {{-- Kiosk self-reset: nobody's meant to stay on this screen — either
             they print and move on, or they walk away and the machine needs
             to be ready for the next visitor on its own. --}}
        <p id="auto-redirect-notice" class="mt-4 text-center text-sm text-gray-400 dark:text-gray-500">
            Returning to the kiosk in <span id="auto-redirect-seconds" class="font-semibold tabular-nums">15</span>s…
        </p>
    </x-page-shell>
</x-guest-layout>
