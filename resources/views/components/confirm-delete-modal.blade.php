@props(['name', 'action', 'title' => 'Delete this?', 'body' => 'This action cannot be undone.', 'ajax' => false])

{{-- Every delete anywhere in the app goes through this instead of the
     browser's native confirm() — a real dialog that matches the rest of
     the UI, not a jarring OS-styled popup. The Delete button inside IS the
     confirmation; the form only submits once someone clicks it here.

     `ajax`: submit with fetch (admin-forms.js) and reload on success,
     rather than a normal form post + redirect. Used by the staff and
     service tables, whose delete endpoints answer in JSON. --}}
<x-modal :name="$name" focusable max-width="sm">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                <x-icon.trash class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                <p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $body }}</p>
            </div>
        </div>

        <form method="POST" action="{{ $action }}"
              @if ($ajax) data-ajax-form data-action="{{ $action }}" @endif
              class="mt-6">
            @csrf
            @method('DELETE')

            @if ($ajax)
                <ul data-form-errors hidden role="alert"
                    class="mb-4 list-inside list-disc space-y-1 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200"></ul>
            @endif

            <div class="flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-modal>
