@props(['name', 'title', 'action', 'method' => 'POST', 'submit' => 'Save'])

{{-- The dialog shell shared by every admin create/edit form (staff,
     services): title, an inline error list, the fields (slot), and
     Cancel / submit. admin-forms.js picks it up via `data-ajax-form`,
     submits it with fetch, fills the error list on a validation failure,
     and reloads on success. --}}
<x-modal :name="$name" focusable>
    <form method="POST" action="{{ $action }}" data-ajax-form data-action="{{ $action }}" class="p-6" novalidate>
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>

        <ul data-form-errors hidden role="alert"
            class="mt-4 list-inside list-disc space-y-1 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200"></ul>

        <div class="mt-5 space-y-5">
            {{ $slot }}
        </div>

        <div class="mt-7 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $submit }}</x-primary-button>
        </div>
    </form>
</x-modal>
