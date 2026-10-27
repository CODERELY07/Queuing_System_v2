@php
    // Sample data for the illustrations on this page — illustrative, not
    // live. (The real board is /display; the real ticket comes from /kiosk.)
    $previewBoard = [
        ['name' => 'Registration', 'number' => 'R-014', 'called' => true],
        ['name' => 'Pharmacy', 'number' => 'P-021', 'called' => false],
        ['name' => 'Emergency', 'number' => 'E-003', 'called' => false],
    ];

    // The kiosk illustration uses the clinic's real departments when there
    // are any, so it never shows a service the kiosk doesn't offer.
    $kioskNames = $services->pluck('name')->take(4)->values();
    if ($kioskNames->isEmpty()) {
        $kioskNames = collect(['Registration', 'Doctor Consultation', 'Pharmacy', 'Emergency']);
    }

    $faqs = [
        ['q' => 'Do I need to install an app?', 'a' => 'No. Take a ticket at the kiosk, then watch the waiting-room display or listen for your number.'],
        ['q' => 'What if I don\'t hear my number?', 'a' => 'Check with the counter. Staff can call your number again or bring you back into the line.'],
        ['q' => 'Is there a priority lane?', 'a' => 'Yes. Senior citizens, PWDs and pregnant visitors can tick the priority box at intake and are called ahead of the regular queue.'],
        ['q' => 'Can I see the queue from my phone?', 'a' => 'The queue display is a public page. Anyone with the link can open it on a phone or another screen in the building.'],
        ['q' => 'How does staff decide who to call next?', 'a' => 'Priority-lane tickets first, then oldest ticket first within each lane: the same order the display shows.'],
    ];
@endphp

<x-guest-layout :flush="true">
    {{-- Hero ------------------------------------------------------------ --}}
    <section class="grid items-center gap-12 py-12 sm:py-16 lg:grid-cols-12 lg:gap-8 lg:py-24">
        <div class="animate-fade-up lg:col-span-6">
            <h1 class="font-display text-5xl font-medium leading-[1.04] tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                Take a number.<br>
                <em class="text-brand-700">Then take a seat.</em>
            </h1>

            <p class="mt-6 max-w-lg text-lg leading-relaxed text-gray-600">
                MedQueue prints your ticket at the kiosk, shows your number on the waiting-room screen, and says it out loud the moment it's your turn. Nobody hovers by the counter.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <x-cta-button :href="route('kiosk')" size="lg" class="w-full sm:w-auto">
                    Get a queue number
                    <x-icon.arrow-right class="h-4 w-4" />
                </x-cta-button>
                <x-cta-button :href="route('display')" variant="outline" size="lg" class="w-full sm:w-auto">
                    See the display
                </x-cta-button>
            </div>

            @if ($services->isNotEmpty())
                <p class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-600">
                    <span class="text-gray-500">Open today</span>
                    @foreach ($services as $service)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="font-mono text-xs font-medium text-brand-700">{{ $service->prefix }}</span>
                            {{ $service->name }}
                        </span>
                    @endforeach
                </p>
            @endif
        </div>

        {{-- A paper ticket, a served one behind it, and the announcement
             that goes with it. Pure markup + CSS (see .ticket-* in app.css). --}}
        <div class="animate-fade-up lg:col-span-6" style="animation-delay: 80ms">
            <div class="relative mx-auto h-[27rem] w-full max-w-sm sm:h-[28rem] sm:max-w-md" aria-hidden="true">
                <div class="dot-grid absolute inset-x-1 bottom-2 top-10 rounded-[28px] border border-brand-100 bg-brand-50"></div>

                {{-- The previous ticket, already served. Hidden on phones,
                     where there isn't room for it beside the new one. --}}
                <div class="absolute left-2 top-24 hidden w-52 -rotate-6 rounded-xl border border-gray-200 bg-white p-4 shadow-md sm:block">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-gray-400">Registration</span>
                    <p class="mt-3 font-mono text-4xl font-medium tracking-tight text-gray-300 line-through decoration-2">R-013</p>
                    <x-status-badge status="finish" class="mt-3" />
                </div>

                {{-- What the screen says out loud. --}}
                <div class="absolute right-0 top-0 w-56 rounded-2xl border border-gray-200 bg-white p-4 shadow-lg">
                    <p class="flex items-center gap-1.5 text-[11px] font-medium uppercase tracking-wider text-gray-500">
                        <x-icon.volume class="h-3.5 w-3.5 text-brand-700" /> Read aloud
                    </p>
                    <p class="mt-1.5 font-display text-xl italic leading-snug text-gray-900">&ldquo;R-014, please come to counter two.&rdquo;</p>
                </div>

                {{-- The ticket in hand. --}}
                <div class="absolute bottom-0 right-1 w-64 rotate-[2.5deg] drop-shadow-[0_16px_22px_rgba(28,27,25,0.16)] sm:right-3 sm:w-72">
                    <div class="ticket-top bg-white px-6 pb-6 pt-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold tracking-tight text-gray-900">MedQueue</span>
                            <span class="font-mono text-[11px] uppercase tracking-wider text-gray-500">Registration</span>
                        </div>
                        <p class="mt-5 text-[11px] font-medium uppercase tracking-wider text-gray-500">Your number</p>
                        <p class="font-mono text-6xl font-medium leading-none tracking-tight text-brand-800">R-014</p>
                    </div>

                    <div class="ticket-bottom relative bg-white px-6 pb-5 pt-6">
                        <span class="absolute inset-x-5 top-0 border-t-2 border-dashed border-gray-300"></span>
                        <dl class="space-y-2 text-sm">
                            <div class="flex items-baseline justify-between"><dt class="text-gray-500">Ahead of you</dt><dd class="font-mono font-medium text-gray-900">6</dd></div>
                            <div class="flex items-baseline justify-between"><dt class="text-gray-500">Est. wait</dt><dd class="font-mono font-medium text-gray-900">12 min</dd></div>
                            <div class="flex items-baseline justify-between"><dt class="text-gray-500">Issued</dt><dd class="font-mono font-medium text-gray-900">09:41</dd></div>
                        </dl>
                        <div class="ticket-barcode mt-4 h-8 w-full opacity-80"></div>
                    </div>
                </div>
            </div>
            <p class="mt-3 text-center text-xs text-gray-500">Sample ticket, for illustration.</p>
        </div>
    </section>

    {{-- The journey ----------------------------------------------------- --}}
    <section class="border-t border-gray-200 py-16 sm:py-20">
        <h2 class="max-w-xl font-display text-3xl font-medium tracking-tight text-gray-900 sm:text-4xl">One number, three places it shows up.</h2>

        <ol class="mt-12 grid gap-12 md:grid-cols-3 md:gap-8">
            <li>
                <div class="md:h-48" aria-hidden="true">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-medium text-gray-500">Select service</p>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach ($kioskNames as $i => $name)
                            <span class="truncate rounded-lg border-2 px-2.5 py-2 text-xs font-medium {{ $i === min(1, $kioskNames->count() - 1) ? 'border-brand-600 bg-brand-50 text-brand-900' : 'border-gray-200 text-gray-700' }}">{{ $name }}</span>
                        @endforeach
                    </div>
                    <div class="mt-2 flex items-center gap-2 rounded-lg border border-gray-200 px-2.5 py-2 text-xs text-gray-600">
                        <span class="h-3.5 w-3.5 rounded border border-gray-300"></span> Priority lane
                    </div>
                </div>
                </div>
                <p class="mt-5 font-mono text-xs font-medium uppercase tracking-wider text-brand-700">At the kiosk</p>
                <h3 class="mt-1.5 text-lg font-semibold text-gray-900">Pick a service, get a ticket</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Enter your name and tap a department. Seniors, PWDs and pregnant visitors can use the priority lane.</p>
            </li>

            <li>
                <div class="md:h-48" aria-hidden="true">
                <ul class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white shadow-sm">
                    @foreach ($previewBoard as $row)
                        <li class="relative flex items-center justify-between gap-4 px-4 py-3 {{ $row['called'] ? 'bg-brand-50' : '' }}">
                            @if ($row['called'])
                                <span class="absolute inset-y-0 left-0 w-0.5 bg-brand-600"></span>
                            @endif
                            <span class="text-xs font-medium {{ $row['called'] ? 'text-gray-900' : 'text-gray-500' }}">{{ $row['name'] }}</span>
                            <span class="font-mono text-lg font-medium tabular-nums {{ $row['called'] ? 'text-brand-800' : 'text-gray-900' }}">{{ $row['number'] }}</span>
                        </li>
                    @endforeach
                </ul>
                </div>
                <p class="mt-5 font-mono text-xs font-medium uppercase tracking-wider text-brand-700">On the screen</p>
                <h3 class="mt-1.5 text-lg font-semibold text-gray-900">Watch for your number</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">It appears on the waiting-room display and is read aloud when it's called. No hovering by the counter.</p>
            </li>

            <li>
                <div class="md:h-48" aria-hidden="true">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <x-status-badge status="waiting" />
                        <x-icon.arrow-right class="h-3 w-3 text-gray-300" />
                        <x-status-badge status="serving" />
                        <x-icon.arrow-right class="h-3 w-3 text-gray-300" />
                        <x-status-badge status="finish" />
                    </div>
                    <div class="mt-3 flex h-9 items-center justify-center gap-2 rounded-lg bg-brand-700 text-sm font-medium text-white">
                        Next patient
                        <kbd class="rounded border border-white/30 px-1.5 font-mono text-[10px] text-white/80">Space</kbd>
                    </div>
                </div>
                </div>
                <p class="mt-5 font-mono text-xs font-medium uppercase tracking-wider text-brand-700">At the counter</p>
                <h3 class="mt-1.5 text-lg font-semibold text-gray-900">Staff see exactly who's next</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">One button calls the next patient, in the right order, and every screen in the building updates at once.</p>
            </li>
        </ol>
    </section>

    {{-- What it does ---------------------------------------------------- --}}
    <section class="border-t border-gray-200 py-16 sm:py-20">
        <div class="max-w-2xl">
            <h2 class="font-display text-3xl font-medium tracking-tight text-gray-900 sm:text-4xl">Everything the front desk needs, nothing visitors have to install.</h2>
        </div>

        <div class="mt-10 grid gap-4 lg:grid-cols-6">
            <article class="flex rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-4">
                <div class="grid w-full items-center gap-6 sm:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">A display that speaks</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">The waiting-room screen updates the moment staff call the next number, with a spoken announcement as well as text, so you can look up from your phone and still catch it.</p>
                    </div>
                    <figure class="rounded-xl bg-brand-50 p-5">
                        <x-icon.volume class="h-5 w-5 text-brand-700" />
                        <blockquote class="mt-3 font-display text-2xl italic leading-snug text-brand-900">&ldquo;D-007, please come to counter two.&rdquo;</blockquote>
                    </figure>
                </div>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900">A fair priority lane</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Priority visitors are called ahead of the regular line, then in ticket order.</p>
                <ol class="mt-4 space-y-1.5 text-sm" aria-label="Example calling order">
                    <li class="flex items-center justify-between rounded-lg bg-violet-50/60 px-3 py-2"><span class="font-mono font-medium text-gray-900">R-012</span><x-priority-badge /></li>
                    <li class="flex items-center justify-between rounded-lg px-3 py-2"><span class="font-mono font-medium text-gray-900">R-009</span><span class="text-xs text-gray-500">then</span></li>
                    <li class="flex items-center justify-between rounded-lg px-3 py-2"><span class="font-mono font-medium text-gray-900">R-010</span><span class="text-xs text-gray-500">then</span></li>
                </ol>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-3">
                <h3 class="text-lg font-semibold text-gray-900">One kiosk, every service</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Registration, consultation, pharmacy, or a department added later. Admins set them up once and they appear on the kiosk straight away.</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-3">
                <h3 class="text-lg font-semibold text-gray-900">A console built for the counter</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Call, recall or mark a no-show from one screen per counter. Press <kbd class="rounded border border-gray-300 bg-gray-50 px-1.5 font-mono text-xs text-gray-700">Space</kbd> to call the next patient without reaching for the mouse.</p>
            </article>
        </div>
    </section>

    @if ($services->isNotEmpty())
        {{-- Departments, pulled live from what's actually configured --- --}}
        <section class="border-t border-gray-200 py-16 sm:py-20">
            <div class="grid items-start gap-8 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <h2 class="font-display text-3xl font-medium tracking-tight text-gray-900 sm:text-4xl">Open today</h2>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">Each department has its own full-screen board, made to be mounted at that waiting area.</p>
                </div>

                <ul class="grid gap-3 sm:grid-cols-2 lg:col-span-8">
                    @foreach ($services as $service)
                        <li>
                            <a href="{{ route('display.show', $service) }}"
                               class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-50 font-mono text-lg font-medium text-brand-800">{{ $service->prefix }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-gray-900">{{ $service->name }}</span>
                                    <span class="block text-xs text-gray-500">Open board</span>
                                </span>
                                <x-icon.arrow-right class="h-4 w-4 shrink-0 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-brand-700" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- FAQ ------------------------------------------------------------- --}}
    <section class="border-t border-gray-200 py-16 sm:py-20">
        <div class="grid gap-10 lg:grid-cols-12">
            <h2 class="font-display text-3xl font-medium tracking-tight text-gray-900 sm:text-4xl lg:col-span-4">Common questions</h2>

            <div class="divide-y divide-gray-200 border-y border-gray-200 lg:col-span-8">
                @foreach ($faqs as $i => $faq)
                    <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }" class="py-4">
                        <h3>
                            <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 text-left" :aria-expanded="open.toString()" aria-controls="faq-{{ $i }}">
                                <span class="font-medium text-gray-900">{{ $faq['q'] }}</span>
                                <x-icon.chevron-down class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                            </button>
                        </h3>
                        <p id="faq-{{ $i }}" x-show="open" x-cloak
                           x-transition:enter="transition ease-out duration-150"
                           x-transition:enter-start="opacity-0 -translate-y-1"
                           x-transition:enter-end="opacity-100 translate-y-0"
                           class="mt-2 pr-8 text-sm leading-relaxed text-gray-600">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Closing --------------------------------------------------------- --}}
    <section class="mb-16 flex flex-col gap-6 rounded-3xl border border-brand-100 bg-brand-50 px-6 py-10 sm:px-10 sm:py-12 md:flex-row md:items-center md:justify-between">
        <div class="max-w-md">
            <h2 class="font-display text-3xl font-medium tracking-tight text-gray-900 sm:text-4xl">Ready when you are.</h2>
            <p class="mt-2 text-gray-600">Take a ticket at the kiosk, or sign in if you're running a counter today.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-cta-button :href="route('kiosk')" size="lg">Get a queue number</x-cta-button>
            <x-cta-button :href="route('login')" variant="outline" size="lg">Staff login</x-cta-button>
        </div>
    </section>
</x-guest-layout>
