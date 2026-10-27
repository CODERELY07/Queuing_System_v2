@php $repository = config('demo.repository'); @endphp

{{-- White, like the header: a hairline rule on the paper-colored page, not a
     dark block. Three short columns instead of one long row so it reads
     the same on a phone (stacked) and a laptop (side by side). --}}
<footer class="border-t border-gray-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 pb-8 pt-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12">
            <div class="sm:col-span-2 lg:col-span-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5" aria-label="MedQueue home">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-white">
                        <x-application-logo class="h-[18px] w-[18px] text-white" />
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight text-gray-900">MedQueue</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-gray-600">
                    A real-time queue for clinics, pharmacies and hospital front desks: a ticket kiosk, a waiting-room display and a counter console that stay in sync.
                </p>
            </div>

            <nav aria-label="Visitors" class="lg:col-span-3 lg:col-start-7">
                <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">For visitors</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="{{ route('kiosk') }}" class="text-gray-700 transition hover:text-brand-700">Take a ticket</a></li>
                    <li><a href="{{ route('display') }}" class="text-gray-700 transition hover:text-brand-700">Waiting-room display</a></li>
                </ul>
            </nav>

            <nav aria-label="Team" class="lg:col-span-3">
                <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">For the team</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="{{ route('login') }}" class="text-gray-700 transition hover:text-brand-700">Staff login</a></li>
                    @if ($repository)
                        <li><a href="{{ $repository }}" target="_blank" rel="noopener noreferrer" class="text-gray-700 transition hover:text-brand-700">Source code</a></li>
                    @endif
                </ul>
            </nav>
        </div>

        <div class="mt-12 flex flex-col gap-2 border-t border-gray-200 pt-6 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} MedQueue</p>
            <p>A portfolio project &mdash; not a real clinic.</p>
        </div>
    </div>
</footer>
