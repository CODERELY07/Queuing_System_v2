@php
    // Portfolio project only: every account in config/demo.php is seeded demo
    // data, not a real clinic, so there's nothing sensitive about listing the
    // credentials. It exists so anyone looking at the portfolio can try every
    // role without digging through the README. DEMO_ACCOUNTS=false hides it.
    $demo = config('demo.enabled');
    $demoGroups = $demo ? collect(config('demo.accounts'))->groupBy('user_type') : collect();
@endphp

<x-guest-layout title="Staff login" :hide-footer="true">
    {{-- Wide enough for form + demo panel side by side from `md` (tablet)
         up; a plain narrow column below that, and whenever the panel is off. --}}
    <x-page-shell :max-width="$demo ? 'max-w-md md:max-w-4xl' : 'max-w-md'">
        {{-- Alpine state lives on this wrapper (not the form card) so the
             demo panel, now a sibling of the card, can still fill the form. --}}
        <div x-data="{
                email: @js(old('email', '')),
                password: '',
                sending: false,
                // Demo accounts fill the form and submit it in one step.
                signIn(account) {
                    this.email = account.email;
                    this.password = account.password;
                    this.$nextTick(() => this.$refs.form.requestSubmit());
                },
             }"
             x-init="
                // Focus the email field on tablet/laptop only. On a phone it
                // opens the keyboard over the page, hiding the demo accounts.
                if (window.matchMedia('(min-width: 768px)').matches) $refs.email.focus();
             ">
            <div class="mb-8 text-center">
                <x-eyebrow-badge>Staff</x-eyebrow-badge>
                <h1 class="mt-3 font-display text-4xl font-medium tracking-tight text-gray-900 dark:text-white">Sign in</h1>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Run your counter or manage the clinic.</p>

                @if ($demo)
                    <a href="#demo-accounts" class="mt-4 inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-sm font-medium text-brand-700 hover:bg-brand-50 md:hidden">
                        Just exploring? Try a demo account
                        <x-icon.arrow-right class="h-3.5 w-3.5 rotate-90" />
                    </a>
                @endif
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="grid gap-6 {{ $demo ? 'md:grid-cols-5 md:items-start' : '' }}">
                <x-card class="{{ $demo ? 'md:col-span-3' : '' }}">
                    <form x-ref="form" method="POST" action="{{ route('login') }}" class="space-y-5" @submit="sending = true">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" x-ref="email" class="mt-1 block w-full" type="email" name="email" x-model="email" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" x-model="password" required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <label for="remember_me" class="inline-flex items-center gap-2">
                            <input id="remember_me" type="checkbox" class="rounded" name="remember">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>

                        <x-cta-button type="submit" class="w-full" ::disabled="sending">
                            <span x-show="!sending">{{ __('Log in') }}</span>
                            <span x-show="sending" x-cloak>{{ __('Signing in…') }}</span>
                        </x-cta-button>
                    </form>
                </x-card>

                @if ($demo)
                    {{-- A dashed, tinted surface (vs. the form's solid white
                         sheet) so it reads as "demo", not as part of the real
                         sign-in. `scroll-mt-20` clears the sticky header when
                         the mobile jump link lands here. --}}
                    <aside id="demo-accounts" aria-labelledby="demo-accounts-heading"
                           class="scroll-mt-20 rounded-2xl border border-dashed border-gray-300 bg-gray-100/70 p-4 sm:p-5 md:col-span-2">
                        <div class="flex items-center gap-2">
                            <h2 id="demo-accounts-heading" class="text-sm font-semibold text-gray-900">Demo accounts</h2>
                            <span class="rounded-md bg-white px-1.5 py-0.5 text-[11px] font-medium text-gray-600 ring-1 ring-inset ring-gray-200">Portfolio</span>
                        </div>
                        <p class="mt-1 text-xs leading-relaxed text-gray-500">Not a real clinic. Tap an account to sign in as them.</p>

                        @foreach ($demoGroups as $type => $accounts)
                            {{-- Show the password once per group when every account in
                                 it shares one, instead of repeating it on each row. --}}
                            @php $sharedPassword = $accounts->pluck('password')->unique()->count() === 1 ? $accounts->first()['password'] : null; @endphp
                            <div class="mt-4">
                                <div class="mb-1.5 flex flex-wrap items-baseline justify-between gap-x-2 gap-y-0.5 px-0.5">
                                    <h3 class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ $type === 'admin' ? 'Administrator' : 'Staff' }}</h3>
                                    @if ($sharedPassword)
                                        <p class="whitespace-nowrap font-mono text-[11px] text-gray-500">password <span class="select-all text-gray-700">{{ $sharedPassword }}</span></p>
                                    @endif
                                </div>

                                <ul class="space-y-1.5">
                                    @foreach ($accounts as $account)
                                        <li>
                                            <button type="button"
                                                    @click="signIn(@js(['email' => $account['email'], 'password' => $account['password']]))"
                                                    :disabled="sending"
                                                    aria-label="{{ __('Log in as :role', ['role' => $account['role']]) }}"
                                                    class="group flex min-h-[3rem] w-full items-center gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left transition hover:border-brand-300 hover:bg-brand-50/50 active:scale-[0.99] disabled:opacity-60">
                                                <span class="min-w-0 flex-1">
                                                    <span class="block truncate text-sm font-medium text-gray-900">{{ $account['service'] === 'Admin' ? 'Admin console' : $account['service'] }}</span>
                                                    <span class="block truncate font-mono text-[11px] text-gray-500">{{ $account['email'] }}@unless ($sharedPassword) &middot; {{ $account['password'] }}@endunless</span>
                                                </span>
                                                <x-icon.arrow-right class="h-4 w-4 shrink-0 text-gray-300 transition group-hover:text-brand-700" />
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </aside>
                @endif
            </div>
        </div>
    </x-page-shell>
</x-guest-layout>
