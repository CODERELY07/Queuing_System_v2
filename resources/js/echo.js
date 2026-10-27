import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Without a Reverb key there is no websocket to connect to. Echo is left
// undefined, and lib/realtime.js treats that as "never connected" — pages
// then fall back to polling instead of erroring.
if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        // Pusher's usage-stats beacon isn't wanted (and Reverb has no
        // stats endpoint to receive it).
        disableStats: true,
    });
} else {
    console.info('Reverb app key is missing — realtime is off, pages will poll instead.');
}
