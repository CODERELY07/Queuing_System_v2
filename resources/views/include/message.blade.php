{{-- Flash messages from redirect()->with('success'|'error', ...) — shown
     for a few seconds, then dismissed. Same two tones as the toasts. --}}
@foreach ([
    'error' => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200',
    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200',
] as $key => $tone)
    @if (session($key))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
             role="{{ $key === 'error' ? 'alert' : 'status' }}"
             class="mb-4 flex items-start justify-between gap-3 rounded-xl border px-4 py-3 text-sm {{ $tone }}">
            <p>{{ session($key) }}</p>
            <button type="button" @click="show = false" class="shrink-0 opacity-60 transition hover:opacity-100" aria-label="{{ __('Dismiss') }}">
                <x-icon.x class="h-4 w-4" />
            </button>
        </div>
    @endif
@endforeach
