/**
 * Non-blocking notifications, replacing the browser's native alert() —
 * which froze the whole page (including the live queue) until dismissed
 * and looked like nothing else in the app.
 */

const STORAGE_KEY = 'medqueue:queued-toast';

const TONES = {
    success: { dot: 'bg-emerald-500', label: 'Success' },
    error: { dot: 'bg-rose-500', label: 'Error' },
    info: { dot: 'bg-brand-500', label: 'Notice' },
};

function region() {
    let el = document.getElementById('toast-region');

    if (!el) {
        el = document.createElement('div');
        el.id = 'toast-region';
        el.className = 'pointer-events-none fixed inset-x-0 bottom-0 z-[60] flex flex-col items-center gap-2 p-4 sm:items-end sm:p-6';
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        document.body.appendChild(el);
    }

    return el;
}

/**
 * @param {string} message Plain text — set via textContent, never parsed as HTML.
 * @param {'success'|'error'|'info'} [type]
 * @param {number} [duration] ms before it dismisses itself; 0 keeps it until clicked.
 */
export function toast(message, type = 'info', duration = 4500) {
    const tone = TONES[type] ?? TONES.info;

    const item = document.createElement('div');
    item.className = 'animate-toast-in pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-800 shadow-lg dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100';

    const dot = document.createElement('span');
    dot.className = `mt-1.5 h-2 w-2 shrink-0 rounded-full ${tone.dot}`;
    dot.setAttribute('aria-hidden', 'true');

    const text = document.createElement('p');
    text.className = 'min-w-0 flex-1 leading-snug';
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'shrink-0 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200';
    close.setAttribute('aria-label', 'Dismiss');
    close.innerHTML = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

    const dismiss = () => item.remove();
    close.addEventListener('click', dismiss);

    item.append(dot, text, close);
    region().appendChild(item);

    if (duration > 0) {
        setTimeout(dismiss, duration);
    }

    return item;
}

/**
 * Show a toast after the next page load — for actions that finish by
 * reloading the page, which would otherwise wipe the confirmation.
 */
export function toastAfterReload(message, type = 'success') {
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ message, type }));
    } catch {
        /* storage unavailable (private mode) — the reload just goes unannounced */
    }
}

export function flushQueuedToast() {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (!raw) return;

        sessionStorage.removeItem(STORAGE_KEY);
        const { message, type } = JSON.parse(raw);
        toast(message, type);
    } catch {
        /* nothing queued, or storage unavailable */
    }
}
