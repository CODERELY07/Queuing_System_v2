<x-guest-layout :flush="true" :hide-footer="true" :hide-header="true">
    {{-- The dedicated, one-department screen: meant to be mounted at that
         department's own waiting area, so the number is the whole point —
         no shared grid, no other departments competing for attention. --}}
    <div id="single-display" data-service-id="{{ $service->id }}" data-service-slug="{{ $service->slug }}" class="min-h-screen flex flex-col bg-white">
        <div class="bg-brand-600 py-6 px-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-white text-center uppercase tracking-wide">{{ $service->name }}</h1>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center px-8 text-center">
            <p class="text-xl sm:text-2xl font-semibold text-gray-400 uppercase tracking-widest mb-6">Now Serving</p>
            <p id="single-serving-number"
               class="font-mono tabular-nums font-bold text-gray-900 leading-none animate-flash-once"
               style="font-size: clamp(8rem, 22vw, 20rem);">&mdash;</p>
            <p id="single-serving-name" class="text-3xl sm:text-4xl text-gray-500 font-medium mt-8">No patient being served</p>
        </div>

        <div class="border-t border-gray-100 bg-gray-50 py-8 px-8">
            <p class="text-lg sm:text-xl font-semibold uppercase tracking-widest text-gray-400 text-center mb-3">Up Next</p>
            <p id="single-next" class="font-mono tabular-nums text-3xl sm:text-4xl text-gray-600 text-center">&mdash;</p>
        </div>
    </div>
</x-guest-layout>
