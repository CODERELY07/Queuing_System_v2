{{-- Callers always pass their own `class` for sizing/color — kept out of
     the defaults here so an override can never collide with a baked-in
     size class (Tailwind's cascade order isn't attribute order). --}}
<svg {{ $attributes->merge(['stroke-width' => '2']) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
</svg>
