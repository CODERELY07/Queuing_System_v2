@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1 text-sm text-rose-700 dark:text-rose-400']) }} role="alert">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
