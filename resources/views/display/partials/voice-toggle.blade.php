{{-- Mute switch for the spoken announcements — see display.js. Browsers
     won't speak until the page has had a click, so on an unattended screen
     the first press here is also what switches announcements on.

     `light`: pass true on the full-screen department display, which is a
     fixed light surface regardless of the OS theme. --}}
@php($light = $light ?? false)

<button type="button" id="voice-toggle" aria-pressed="true"
        @class([
            'inline-flex h-9 items-center gap-2 rounded-lg border px-3 text-sm font-medium shadow-sm transition',
            'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' => $light,
            'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' => !$light,
        ])>
    <span data-dot class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
    <span data-label>Announcements on</span>
</button>
