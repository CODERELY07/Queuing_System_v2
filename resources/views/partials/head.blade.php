{{-- The one <head> both layouts share — it used to be copy-pasted into
     layouts/app and layouts/guest, which is how the two drifted apart. --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="color-scheme" content="{{ $scheme ?? 'light dark' }}">

<title>{{ filled($title ?? null) ? $title . ' · ' . config('app.name', 'MedQueue') : config('app.name', 'MedQueue') }}</title>

<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23245A60' stroke-width='2'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M4 12h4l2-6 3 12 2-6h5'/%3E%3C/svg%3E">

{{-- Three families, each used for one job: Instrument Sans for UI text,
     IBM Plex Mono for ticket numbers, Newsreader for public-page
     headlines. Only the weights actually referenced are requested. --}}
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|ibm-plex-mono:400,500,600|newsreader:500,500i&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])
