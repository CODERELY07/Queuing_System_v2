<x-guest-layout :flush="true">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center py-4 animate-fade-up">
        <!-- Copy column -->
        <div>
            <x-eyebrow-badge class="mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Digital Queuing System
            </x-eyebrow-badge>

            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight mb-4">
                <span class="block text-gray-900 dark:text-white">Know your place</span>
                <span class="block text-brand-600 dark:text-brand-400">before you arrive.</span>
            </h1>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-3">
                Less waiting-room anxiety. More time for what matters.
            </p>
            <p class="text-gray-600 dark:text-gray-400 max-w-md mb-8">
                Take a ticket at the kiosk, watch your number on the waiting-room display, and we'll call you the moment it's your turn — no hovering by the counter required.
            </p>

            <div class="flex flex-wrap items-center gap-3">
                <x-cta-button :href="route('kiosk')" size="lg">
                    Get a Queue Number
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </x-cta-button>
                <x-cta-button :href="route('display')" variant="outline" size="lg">
                    View the display
                </x-cta-button>
            </div>
        </div>

        <!-- Illustration column -->
        <div class="relative">
            <div class="rounded-3xl bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-900/50 overflow-hidden">
                <svg viewBox="0 0 480 380" class="w-full h-auto" role="img" aria-label="Two visitors sitting in a clinic waiting room, one checking their ticket number on their phone">
                    <!-- Door, on the wall behind the bench -->
                    <rect x="360" y="40" width="88" height="200" rx="10" class="fill-none stroke-brand-200 dark:stroke-brand-800" stroke-width="3" />
                    <circle cx="404" cy="60" r="14" class="fill-brand-100 dark:fill-brand-900/60 stroke-brand-300 dark:stroke-brand-700" stroke-width="2" />
                    <path d="M404 54v12M398 60h12" class="stroke-brand-500" stroke-width="2.5" stroke-linecap="round" />

                    <!-- Wall clock -->
                    <circle cx="70" cy="70" r="26" class="fill-white dark:fill-gray-800 stroke-brand-200 dark:stroke-brand-800" stroke-width="3" />
                    <path d="M70 56v16l12 8" class="stroke-brand-500" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />

                    <!-- Potted plant -->
                    <path d="M150 250c-18 0-30-16-26-34 10 4 18 14 20 26 2-14 12-24 24-26 4 18-6 34-18 34z" class="fill-brand-200 dark:fill-brand-800" />
                    <path d="M150 250c-14 4-24 18-22 34 8 2 16-2 22-8 6 6 14 10 22 8 2-16-8-30-22-34z" class="fill-brand-300 dark:fill-brand-700" />
                    <path d="M138 284h24l-4 28h-16z" class="fill-brand-400 dark:fill-brand-600" />

                    <!-- Bench -->
                    <rect x="130" y="280" width="220" height="14" rx="6" class="fill-gray-300 dark:fill-gray-700" />
                    <rect x="140" y="294" width="10" height="34" rx="3" class="fill-gray-300 dark:fill-gray-700" />
                    <rect x="330" y="294" width="10" height="34" rx="3" class="fill-gray-300 dark:fill-gray-700" />

                    <!-- Visitor one, arms free -->
                    <circle cx="195" cy="222" r="20" class="fill-brand-600" />
                    <rect x="167" y="240" width="56" height="46" rx="18" class="fill-brand-600" />

                    <!-- Visitor two, holding a phone -->
                    <circle cx="278" cy="222" r="20" class="fill-gray-400 dark:fill-gray-500" />
                    <rect x="250" y="240" width="56" height="46" rx="18" class="fill-gray-400 dark:fill-gray-500" />
                    <rect x="270" y="252" width="20" height="30" rx="4" class="fill-white dark:fill-gray-200 stroke-gray-500" stroke-width="1.5" />

                    <!-- Ticket notification bubble -->
                    <rect x="298" y="196" width="70" height="34" rx="10" class="fill-white dark:fill-gray-100 stroke-brand-300" stroke-width="1.5" />
                    <path d="M310 230l-8 12v-12z" class="fill-white dark:fill-gray-100 stroke-brand-300" stroke-width="1.5" />
                    <text x="333" y="218" text-anchor="middle" class="fill-brand-700 font-mono font-bold" style="font-size:13px">R-047</text>
                </svg>
            </div>

            <!-- Floating confirmation card -->
            <x-info-callout class="absolute -bottom-6 -left-4 sm:left-4 max-w-[15rem] shadow-lg">
                <span class="block font-semibold text-gray-900 dark:text-white">No more guessing.</span>
                Know your number. Get called. Walk in ready.
            </x-info-callout>
        </div>
    </div>

    <!-- Section divider -->
    <div class="flex items-center gap-4 max-w-4xl mx-auto mt-16 mb-8">
        <span class="h-px flex-1 border-t border-dashed border-gray-300 dark:border-gray-700"></span>
        <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">The Journey</span>
        <span class="h-px flex-1 border-t border-dashed border-gray-300 dark:border-gray-700"></span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto pb-8">
        @foreach ([
            ['n' => '1', 'title' => 'Take a ticket', 'body' => 'Enter your name and choose a service at the kiosk.'],
            ['n' => '2', 'title' => 'Watch the display', 'body' => 'Follow your number on the waiting-room screen.'],
            ['n' => '3', 'title' => 'Get served', 'body' => 'Head to the counter when your number is called.'],
        ] as $step)
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/50 p-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-white text-sm font-semibold mb-3">
                    {{ $step['n'] }}
                </span>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step['body'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Features -->
    <div class="py-16 border-t border-gray-100 dark:border-gray-800">
        <div class="text-center max-w-xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3">Everything the front desk needs</h2>
            <p class="text-gray-600 dark:text-gray-400">No separate app to install, no accounts for visitors to create — just a kiosk, a screen, and a counter that all stay in sync.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['icon' => 'monitor', 'title' => 'Live queue display', 'body' => 'A waiting-room screen that updates the moment staff call the next number — with a spoken announcement, not just text on screen.', 'tone' => 'brand'],
                ['icon' => 'bolt', 'title' => 'Priority lane', 'body' => 'Senior citizens, PWDs, and pregnant visitors can flag themselves at intake and get called ahead of the regular line — automatically.', 'tone' => 'violet'],
                ['icon' => 'clipboard', 'title' => 'One kiosk, every service', 'body' => 'Registration, consultation, pharmacy, or a department you add later — visitors pick a service, the kiosk handles the rest.', 'tone' => 'green'],
                ['icon' => 'staff', 'title' => 'Real-time staff console', 'body' => 'Call, recall, or mark a no-show from one screen per counter — every action updates the display instantly for everyone waiting.', 'tone' => 'amber'],
            ] as $feature)
                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/50 p-6">
                    <div @class([
                        'flex h-11 w-11 items-center justify-center rounded-xl mb-4',
                        'bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-400' => $feature['tone'] === 'brand',
                        'bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-400' => $feature['tone'] === 'violet',
                        'bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-400' => $feature['tone'] === 'green',
                        'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400' => $feature['tone'] === 'amber',
                    ])>
                        <x-dynamic-component :component="'icon.' . $feature['icon']" class="h-5 w-5" />
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1.5">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @if ($services->isNotEmpty())
        <!-- Services, pulled live from what's actually configured -->
        <div class="py-16 border-t border-gray-100 dark:border-gray-800">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3">Every department, one kiosk</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md">
                        Services are configured once by an admin and show up on the kiosk immediately — visitors always see what's actually open today, not a stale printed sign.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach ($services as $service)
                        <span class="inline-flex items-center gap-2 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                            {{ $service->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- FAQ -->
    <div class="py-16 border-t border-gray-100 dark:border-gray-800">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white text-center mb-10">Common questions</h2>

        <div class="max-w-2xl mx-auto divide-y divide-gray-200 dark:divide-gray-800 border-t border-b border-gray-200 dark:border-gray-800">
            @foreach ([
                ['q' => 'Do I need to install an app?', 'a' => "No. Take a ticket at the kiosk, then watch the waiting-room display or listen for your number to be called."],
                ['q' => 'What if I don\'t hear my number?', 'a' => 'Check with the counter — staff can call your number again or bring you back into the line.'],
                ['q' => 'Is there a priority lane?', 'a' => 'Yes. Senior citizens, PWDs, and pregnant visitors can check the priority box at intake and are called ahead of the regular queue.'],
                ['q' => 'Can I see the queue without standing near the screen?', 'a' => 'The queue display is a public page — anyone with the link can view it from a phone or another screen in the building.'],
                ['q' => 'How does staff decide who to call next?', 'a' => 'Oldest ticket first within each service, with priority-lane tickets called ahead of the regular line — the same order the display shows.'],
            ] as $i => $faq)
                <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }" class="py-4">
                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 text-left" :aria-expanded="open">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $faq['q'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <p x-show="open"
                       x-transition:enter="transition ease-out duration-150"
                       x-transition:enter-start="opacity-0 -translate-y-1"
                       x-transition:enter-end="opacity-100 translate-y-0"
                       class="text-sm text-gray-600 dark:text-gray-400 mt-2 pr-8">{{ $faq['a'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Closing CTA -->
    <div class="mb-16">
        <div class="rounded-3xl bg-brand-600 px-8 py-12 sm:py-16 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Ready when you are.</h2>
            <p class="text-brand-50 max-w-md mx-auto mb-8">Take a ticket at the kiosk, or sign in if you're staff running a counter today.</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('kiosk') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-brand-50 active:scale-[0.98] text-brand-700 font-semibold text-base py-3 px-6 rounded-lg transition">
                    Get a Queue Number
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 border border-brand-400 hover:bg-brand-500 active:scale-[0.98] text-white font-semibold text-base py-3 px-6 rounded-lg transition">
                    Staff Login
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
