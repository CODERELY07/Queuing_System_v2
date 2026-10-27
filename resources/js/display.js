/**
 * The waiting-room displays: the all-departments board (/display) and each
 * department's own full-screen board (/display/{slug}), plus the spoken
 * "now calling" announcement.
 *
 * Pages are detected by the elements they contain (#serving-list,
 * #single-display), not by URL, so this doesn't care what the routes look
 * like or whether the app is served from a sub-path.
 */
import { byId, escapeHtml, onReady } from './lib/dom';
import { request } from './lib/http';
import { listen, onReconnect, poll } from './lib/realtime';

const VOICE_PREF_KEY = 'medqueue:voice';

/* ------------------------------------------------------------------ */
/* Spoken announcements                                                */
/* ------------------------------------------------------------------ */

const synth = 'speechSynthesis' in window ? window.speechSynthesis : null;

const voice = {
    selected: null,
    enabled: (() => {
        try {
            return localStorage.getItem(VOICE_PREF_KEY) !== 'off';
        } catch {
            return true;
        }
    })(),
};

function pickVoice() {
    if (!synth || voice.selected) return;

    const voices = synth.getVoices();
    if (voices.length === 0) return;

    voice.selected =
        voices.find((v) => v.name === 'Google US English') ||
        voices.find((v) => v.lang === 'en-US') ||
        voices.find((v) => v.lang?.startsWith('en')) ||
        voices[0];
}

function speak(text) {
    if (!synth || !voice.enabled) return;

    pickVoice();

    const utterance = new SpeechSynthesisUtterance(text);
    if (voice.selected) utterance.voice = voice.selected;

    // A rapid second call shouldn't queue up behind the first and get read
    // out a minute late — the newest announcement wins.
    synth.cancel();
    synth.speak(utterance);
}

/**
 * Wires the on-screen mute switch. Browsers refuse speech until the page
 * has had a user gesture, so on an unattended screen the first click on
 * this switch is also what unlocks announcements.
 */
function setUpVoiceToggle() {
    const button = byId('voice-toggle');
    if (!button) return;

    if (!synth) {
        button.hidden = true;
        return;
    }

    const paint = () => {
        button.setAttribute('aria-pressed', String(voice.enabled));
        button.querySelector('[data-label]').textContent = voice.enabled ? 'Announcements on' : 'Announcements off';
        button.querySelector('[data-dot]').classList.toggle('bg-emerald-500', voice.enabled);
        button.querySelector('[data-dot]').classList.toggle('bg-gray-400', !voice.enabled);
    };

    button.addEventListener('click', () => {
        voice.enabled = !voice.enabled;

        try {
            localStorage.setItem(VOICE_PREF_KEY, voice.enabled ? 'on' : 'off');
        } catch {
            /* preference just won't persist */
        }

        paint();
        if (voice.enabled) speak('Announcements on');
    });

    paint();
}

/* ------------------------------------------------------------------ */
/* All-departments board                                               */
/* ------------------------------------------------------------------ */

// Last number shown per department, so only a card whose number actually
// changed replays the flash animation — not every card on every refresh.
const shownNumbers = new Map();

function boardCard(service) {
    const { service_name: name, service_prefix: prefix, serving, next } = service;
    const changed = serving && shownNumbers.get(name) !== serving.number;

    shownNumbers.set(name, serving?.number ?? null);

    const body = serving
        ? `<p class="font-mono text-6xl font-medium tabular-nums tracking-tight text-gray-900 sm:text-7xl ${changed ? 'animate-flash-once' : ''}">${escapeHtml(serving.number)}</p>
           <p class="mt-3 max-w-full truncate text-lg font-medium text-gray-500">${escapeHtml(serving.name)}</p>`
        : `<p class="text-base text-gray-400">No one is being served</p>`;

    const upNext = next?.length
        ? next.map((n) => `<li class="rounded-md bg-white px-2 py-1 font-mono text-sm tabular-nums text-gray-600 ring-1 ring-inset ring-gray-200">${escapeHtml(n)}</li>`).join('')
        : '<li class="text-sm text-gray-400">Nobody waiting</li>';

    return `
        <article class="animate-fade-up flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-3">
                <h2 class="truncate text-sm font-semibold text-gray-900">${escapeHtml(name)}</h2>
                <span class="shrink-0 rounded-md bg-gray-100 px-1.5 py-0.5 font-mono text-[11px] font-medium text-gray-500">${escapeHtml(prefix)}</span>
            </header>
            <div class="flex min-h-40 flex-1 flex-col items-center justify-center px-5 py-6 text-center">${body}</div>
            <footer class="border-t border-gray-100 bg-gray-50 px-5 py-3">
                <p class="mb-2 text-[11px] font-medium uppercase tracking-wider text-gray-400">Up next</p>
                <ul class="flex flex-wrap gap-1.5">${upNext}</ul>
            </footer>
        </article>`;
}

/* ------------------------------------------------------------------ */
/* Refreshing                                                          */
/* ------------------------------------------------------------------ */

let inflight = null;
let retryTimer = null;

/**
 * Fetch and render, cancelling any request still in flight — a burst of
 * events (call, then next) must not let an older, slower response land
 * after a newer one and put a stale number on the screen.
 */
async function refresh(render, url) {
    inflight?.abort();
    inflight = new AbortController();

    try {
        render(await request(url, { signal: inflight.signal }));
    } catch (error) {
        if (error.name === 'AbortError') return;
        console.error('Display refresh failed:', error);

        // One pending retry at a time, however many refreshes fail.
        clearTimeout(retryTimer);
        retryTimer = setTimeout(() => refresh(render, url), 5000);
    }
}

onReady(() => {
    setUpVoiceToggle();

    if (synth) {
        pickVoice();
        synth.addEventListener?.('voiceschanged', pickVoice);
    }

    const list = byId('serving-list');
    const single = byId('single-display');
    if (!list && !single) return;

    let reload;

    if (list) {
        reload = () => refresh((boards) => {
            list.innerHTML = boards.length
                ? boards.map(boardCard).join('')
                : '<p class="col-span-full py-12 text-center text-gray-500">No departments are set up yet.</p>';
        }, '/display/serving-patients');
    } else {
        const numberEl = byId('single-serving-number');
        const nameEl = byId('single-serving-name');
        const nextEl = byId('single-next');
        let lastNumber = null;

        reload = () => refresh((data) => {
            const number = data.serving?.number ?? '—';

            numberEl.textContent = number;
            nameEl.textContent = data.serving?.name ?? 'No one is being served';
            nextEl.textContent = data.next?.length ? data.next.join('  ·  ') : '—';

            if (number !== lastNumber) {
                numberEl.classList.remove('animate-flash-once');
                void numberEl.offsetWidth; // restart the animation
                numberEl.classList.add('animate-flash-once');
                lastNumber = number;
            }
        }, `/display/${single.dataset.serviceSlug}/serving-patient`);
    }

    reload();

    // A call is spoken (on a department's own screen, only its own calls),
    // and both call and next/previous/no-show change what's on screen.
    listen(['.queue.call'], (ticket) => {
        const mine = list || String(single.dataset.serviceId) === String(ticket.service_id);

        if (mine) {
            speak(`${ticket.name}, ticket number ${ticket.queue_number}. Please come in.`);
        }
    });
    listen(['.queue.call', '.queue.next'], reload);

    onReconnect(reload);
    poll(reload);
});
