/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 *
 * (axios used to be imported and attached to `window` here, but nothing in
 * the app ever called it — lib/http.js is the one HTTP client — so it was
 * just ~35 KB of unused script on every page.)
 */
import './echo';
