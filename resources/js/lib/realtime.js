/**
 * Thin wrapper over Laravel Echo that every page script goes through.
 *
 * Two jobs the raw `window.Echo.channel(...)` calls didn't do:
 *
 *  1. Survive Echo being absent. With no Reverb key configured (or the
 *     websocket server down) `window.Echo` is undefined or never
 *     connects; the old code called it unguarded, which threw and took the
 *     rest of the page's script down with it.
 *  2. Fall back to polling. A waiting-room screen that silently stops
 *     updating is worse than one that updates a few seconds late, so while
 *     the socket isn't connected, `poll()` keeps the page fresh over HTTP.
 */

const CHANNEL = 'queue';

const connection = () => window.Echo?.connector?.pusher?.connection ?? null;

export const isConnected = () => connection()?.state === 'connected';

/**
 * Listen for broadcast events on the public queue channel.
 *
 * @param {string[]} events Event names as broadcast, e.g. '.queue.call'.
 * @param {(payload: any) => void} handler
 */
export function listen(events, handler) {
    const channel = window.Echo?.channel(CHANNEL);
    if (!channel) return;

    events.forEach((name) => channel.listen(name, handler));
}

/**
 * Call `handler(isConnected)` whenever the socket's state changes, and
 * once up front with the current state.
 */
export function onConnectionChange(handler) {
    handler(isConnected());
    connection()?.bind('state_change', ({ current }) => handler(current === 'connected'));
}

/**
 * Run `fn` whenever the socket (re)connects — events broadcast while it
 * was down are gone for good, so anything live has to re-fetch its state.
 */
export function onReconnect(fn) {
    connection()?.bind('state_change', ({ previous, current }) => {
        if (current === 'connected' && previous !== 'connected') fn();
    });
}

/**
 * Keep a page fresh while the socket is down: runs `fn` every `intervalMs`
 * as long as the tab is visible and Echo isn't connected. A no-op the rest
 * of the time, so a healthy socket costs nothing extra.
 */
export function poll(fn, intervalMs = 8000) {
    setInterval(() => {
        if (document.hidden || isConnected()) return;
        fn();
    }, intervalMs);
}
