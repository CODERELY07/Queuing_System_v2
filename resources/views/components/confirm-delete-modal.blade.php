@props(['name', 'action', 'title' => 'Delete this?', 'body' => 'This action cannot be undone.', 'formId' => null])

{{-- Every delete anywhere in the app goes through this instead of the
     browser's native confirm() — a real dialog that matches the rest of
     the UI, not a jarring OS-styled popup. The Delete button inside IS the
     confirmation; the form only submits once someone clicks it here. --}}
<x-modal :name="$name" focusable max-width="sm">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400">
                <x-icon.trash class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $body }}</p>
            </div>
        </div>

        <form method="POST" action="{{ $action }}"
              @if ($formId) id="{{ $formId }}" data-action="{{ $action }}" @endif
              class="mt-6 flex justify-end gap-3">
            @csrf
            @method('DELETE')
            <x-secondary-button type="button" x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-danger-button type="submit">
                {{ __('Delete') }}
            </x-danger-button>
        </form>
    </div>
</x-modal>
