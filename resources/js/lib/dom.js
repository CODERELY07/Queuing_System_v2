/**
 * Tiny DOM helpers shared by every page script.
 */

const ESCAPES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

/**
 * Escape a value for safe interpolation into an HTML string.
 *
 * Anything that ends up in `innerHTML` and didn't come from this app's own
 * templates — above all a visitor's name, typed at the public kiosk — must
 * go through this. The waiting-room board interpolated names raw before,
 * which let anyone at the kiosk run script on every screen in the building.
 */
export function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ESCAPES[char]);
}

export const byId = (id) => document.getElementById(id);

/**
 * Run `fn` once the DOM is ready (immediately if it already is).
 */
export function onReady(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn, { once: true });
    } else {
        fn();
    }
}
