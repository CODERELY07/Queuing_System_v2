/**
 * One place that knows how to talk to this app's JSON endpoints — CSRF
 * header, Accept header, error shape — instead of that boilerplate being
 * hand-copied into every fetch() call.
 */

export class HttpError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.name = 'HttpError';
        this.status = status;
        this.errors = errors;
    }
}

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/**
 * @param {string} url
 * @param {{ method?: string, body?: FormData|URLSearchParams|object|null, signal?: AbortSignal, as?: 'json'|'text' }} [options]
 * @returns {Promise<any>} The parsed JSON body (or the raw text with `as: 'text'`,
 *   for the HTML partials the staff dashboard swaps in).
 * @throws {HttpError} On any non-2xx response, with the server's message
 *   (and per-field `errors` for a 422) already extracted.
 */
export async function request(url, { method = 'GET', body = null, signal, as = 'json' } = {}) {
    const headers = {
        Accept: as === 'text' ? 'text/html' : 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    const init = { method, headers, signal, credentials: 'same-origin' };

    if (method !== 'GET') {
        headers['X-CSRF-TOKEN'] = csrfToken();
    }

    if (body instanceof FormData || body instanceof URLSearchParams) {
        init.body = body;
    } else if (body) {
        headers['Content-Type'] = 'application/json';
        init.body = JSON.stringify(body);
    }

    let response;
    try {
        response = await fetch(url, init);
    } catch (error) {
        if (error.name === 'AbortError') throw error;
        throw new HttpError('Network error — check your connection and try again.', 0);
    }

    if (response.ok && as === 'text') {
        return response.text();
    }

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        const firstFieldError = Object.values(payload.errors ?? {}).flat()[0];
        const fallback = response.status === 419
            ? 'Your session expired. Reload the page and try again.'
            : 'Something went wrong. Please try again.';

        throw new HttpError(firstFieldError || payload.message || fallback, response.status, payload.errors ?? {});
    }

    return payload;
}

export const post = (url, body = null) => request(url, { method: 'POST', body });
