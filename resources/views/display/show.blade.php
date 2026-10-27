<x-guest-layout :title="$service->name . ' display'" :flush="true" :hide-footer="true" :hide-header="true">
    {{-- The dedicated, one-department screen: meant to be mounted at that
         department's own waiting area, so the number is the whole point —
         no shared grid, no other departments competing for attention. Fixed
         light surface on purpose (see display.counter). --}}
    <div id="single-display" data-service-id="{{ $service->id }}" data-service-slug="{{ $service->slug }}"
         class="flex min-h-screen flex-col bg-gray-50 text-gray-900">
        <header class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 bg-white px-6 py-5 sm:px-10">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-gray-100 font-mono text-lg font-medium text-gray-600">{{ $service->prefix }}</span>
                <h1 class="font-display text-3xl font-medium tracking-tight sm:text-4xl">{{ $service->name }}</h1>
            </div>

            <div class="flex items-center gap-4">
                <x-live-indicator class="!text-gray-500" />
                @include('display.partials.voice-toggle', ['light' => true])
            </div>
        </header>

        <div class="flex flex-1 flex-col items-center justify-center px-8 py-10 text-center">
            <p class="mb-4 text-lg font-medium uppercase tracking-[0.2em] text-gray-400 sm:text-xl">Now serving</p>
            <p id="single-serving-number"
               class="font-mono font-medium leading-none tracking-tight tabular-nums"
               style="font-size: clamp(7rem, 22vw, 20rem);">&mdash;</p>
            <p id="single-serving-name" class="mt-8 max-w-4xl truncate text-3xl font-medium text-gray-500 sm:text-4xl">No one is being served</p>
        </div>

        <footer class="border-t border-gray-200 bg-white px-8 py-6 text-center">
            <p class="mb-2 text-sm font-medium uppercase tracking-[0.2em] text-gray-400 sm:text-base">Up next</p>
            <p id="single-next" class="font-mono text-3xl tabular-nums text-gray-600 sm:text-4xl">&mdash;</p>
        </footer>
    </div>
</x-guest-layout>
