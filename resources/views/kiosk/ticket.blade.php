@php
    $issuedAt = $queue->created_at;
@endphp

<x-guest-layout title="Your ticket" :hide-footer="true">
    <x-page-shell>
        <x-card class="text-center">
            <x-eyebrow-badge>
                <x-icon.check class="h-3.5 w-3.5" stroke-width="2.5" />
                Ticket confirmed
            </x-eyebrow-badge>

            {{-- The part that's printed — see the @media print rules in
                 app.css. Everything outside #print-body is hidden when the
                 page is printed. --}}
            <div id="print-body" class="mt-5">
                <p class="hidden print:block print:text-lg print:font-semibold">MedQueue</p>

                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 print:text-black">{{ $queue->service->name ?? 'Your queue' }}</p>

                @if ($queue->priority)
                    <div class="mt-2"><x-priority-badge /></div>
                @endif

                <p class="mt-3 animate-flash-once rounded-xl bg-gray-50 py-8 font-mono text-7xl font-medium tabular-nums tracking-tight text-gray-900 dark:bg-gray-900 dark:text-white sm:text-8xl print:bg-white print:py-4 print:text-black">
                    {{ $queue->formattedNumber() }}
                </p>

                <p class="mt-4 text-gray-600 dark:text-gray-400 print:text-black">
                    @if ($estimatedWaitTime === 0)
                        You're next in line.
                    @else
                        Estimated wait: <span class="font-semibold text-gray-900 dark:text-gray-100 print:text-black">about {{ $estimatedWaitTime }} {{ \Illuminate\Support\Str::plural('minute', $estimatedWaitTime) }}</span>
                    @endif
                </p>

                <p class="mt-1 text-xs text-gray-400 print:text-black">
                    Issued {{ $issuedAt->format('M j, g:i A') }} &middot; {{ $queue->name }}
                </p>
            </div>

            <div class="mt-6 flex flex-col items-center justify-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-700 sm:flex-row">
                <x-cta-button id="print">
                    <x-icon.printer class="h-4 w-4" />
                    Print ticket
                </x-cta-button>

                <x-cta-button id="back-to-kiosk" :href="route('kiosk')" variant="outline">
                    Back to kiosk
                </x-cta-button>
            </div>
        </x-card>

        <x-info-callout class="mt-4">
            <span class="block font-semibold text-gray-900 dark:text-white">No more guessing.</span>
            Watch the waiting-room display or listen for your number — we'll call you when it's your turn.
        </x-info-callout>

        {{-- Kiosk self-reset: nobody's meant to stay on this screen —
             either they print and move on, or they walk away and the
             machine needs to be ready for the next visitor on its own. --}}
        <p id="auto-redirect-notice" class="mt-4 text-center text-sm text-gray-400 dark:text-gray-500" aria-live="off">
            Returning to the kiosk in <span id="auto-redirect-seconds" class="font-mono font-semibold tabular-nums">15</span>s…
        </p>
    </x-page-shell>
</x-guest-layout>
