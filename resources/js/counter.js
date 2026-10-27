/**
 * The staff counter dashboard: Next / Previous / Recall / No-show, the
 * per-row "Call" button, and keeping the "now serving", "up next" and
 * ticket-table panels live.
 *
 * Active only on the page that renders #staff-dashboard-meta.
 */
import { byId, onReady } from './lib/dom';
import { post, request } from './lib/http';
import { toast } from './lib/toast';
import { listen, onReconnect, poll } from './lib/realtime';

// Button id -> endpoint. Ids stay as they are in the markup; this is the
// one place that maps them (it used to be an if/else chain inside a
// per-button helper).
const ACTIONS = {
    'call-next-btn': { url: '/staff/call-next', done: (r) => (r.number === 'None' ? 'Nobody is waiting.' : `Now serving ${r.number}`) },
    'call-prev-btn': { url: '/staff/call-previous', done: (r) => (r.number === 'None' ? 'There is no earlier ticket to bring back.' : `Back to ${r.number}`) },
    call: { url: '/staff/call', done: () => 'Announced again.' },
    'skip-btn': { url: '/staff/skip', done: (r) => `${r.number} marked as a no-show.` },
};

const debounce = (fn, ms) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
};

onReady(() => {
    const meta = byId('staff-dashboard-meta');
    if (!meta) return;

    const serviceId = String(meta.dataset.serviceId);

    // One lock for every counter action, not one per button. Without it,
    // clicking "Next Patient" twice before the first request resolves — or
    // "No-show" while "Next" is still in flight — fires overlapping
    // requests against the same ticket, which is exactly how a patient
    // ends up silently skipped or the counter lands in a state nobody
    // actually clicked for.
    let busy = false;

    const controls = () => [
        ...Object.keys(ACTIONS).map(byId).filter(Boolean),
        ...document.querySelectorAll('.selected-call'),
    ];

    function setBusy(value) {
        busy = value;
        controls().forEach((control) => {
            control.disabled = value;
        });
    }

    /* -------------------------------------------------------------- */
    /* Live panels                                                     */
    /* -------------------------------------------------------------- */

    async function loadSnapshot() {
        try {
            const data = await request('/staff/dashboard-data');

            byId('waiting-count').textContent = data.waiting;
            byId('skipped-count').textContent = data.skipped;
            byId('finished-count').textContent = data.finished;

            byId('serving-patient-number').textContent = data.servingPatient.number;
            byId('serving-patient-name').textContent = data.servingPatient.name;
            byId('serving-patient-priority').classList.toggle('hidden', !data.servingPatient.priority);

            // The skeleton only exists for the first load, before we know
            // what's being served — once real content is in it never comes
            // back.
            byId('serving-patient-skeleton')?.classList.add('hidden');
            byId('serving-patient-number').classList.remove('hidden');
            byId('serving-patient-name').classList.remove('hidden');

            const list = byId('recent-activity');
            list.replaceChildren();

            if (data.recent.length === 0) {
                const empty = document.createElement('li');
                empty.className = 'text-gray-400 dark:text-gray-500';
                empty.textContent = 'Nobody is waiting.';
                list.appendChild(empty);
            }

            data.recent.forEach((item, index) => {
                const row = document.createElement('li');
                row.className = 'flex items-center justify-between gap-3 rounded-lg px-3 py-2 ' + (index === 0 ? 'bg-gray-50 dark:bg-gray-800' : '');

                const number = document.createElement('span');
                number.className = 'font-mono tabular-nums text-gray-900 dark:text-gray-100';
                number.textContent = item.number;
                row.appendChild(number);

                if (item.priority) {
                    const badge = document.createElement('span');
                    badge.className = 'text-[11px] font-semibold uppercase tracking-wide text-violet-700 dark:text-violet-300';
                    badge.textContent = 'Priority';
                    row.appendChild(badge);
                }

                list.appendChild(row);
            });
        } catch (error) {
            console.error('Could not load the counter snapshot:', error);
        }
    }

    // Re-renders just the ticket table from a small server-rendered partial,
    // keeping whatever page/sort/search is in the URL. A full navigation
    // would lose scroll position and anything still being typed.
    async function refreshTable() {
        const panel = byId('queue-table-panel');
        if (!panel) return;

        panel.classList.add('opacity-60');

        try {
            panel.innerHTML = await request(`/staff/queue-table${window.location.search}`, { as: 'text' });
        } catch (error) {
            console.error('Could not refresh the ticket table:', error);
        } finally {
            panel.classList.remove('opacity-60');
        }
    }

    const refreshAll = debounce(() => Promise.all([loadSnapshot(), refreshTable()]), 150);

    /* -------------------------------------------------------------- */
    /* Actions                                                         */
    /* -------------------------------------------------------------- */

    async function run(url, describe) {
        if (busy) return;
        setBusy(true);

        try {
            const result = await post(url);
            if (describe) toast(describe(result), 'success', 2500);
        } catch (error) {
            toast(error.message, 'error');
        } finally {
            setBusy(false);
            refreshAll();
        }
    }

    Object.entries(ACTIONS).forEach(([id, { url, done }]) => {
        byId(id)?.addEventListener('click', () => run(url, done));
    });

    // Delegated, so the "Call" buttons keep working after refreshTable()
    // swaps the table's markup — the replacement nodes have no listeners
    // of their own.
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.selected-call');
        if (!button || button.disabled) return;

        run(`/staff/call/${encodeURIComponent(button.dataset.id)}`, () => 'Calling that ticket.');
    });

    // Space calls the next patient without reaching for the mouse — the
    // one shortcut a counter actually repeats all day. Ignored whenever
    // focus is on anything interactive, so it never fires mid-typing or
    // doubles up with a focused button's own Space activation.
    document.addEventListener('keydown', (event) => {
        if (event.code !== 'Space' || event.repeat) return;
        if (event.target.closest('input, textarea, select, button, a, [contenteditable="true"], [role="dialog"]')) return;

        event.preventDefault();
        byId('call-next-btn')?.click();
    });

    /* -------------------------------------------------------------- */
    /* Realtime                                                        */
    /* -------------------------------------------------------------- */

    // Any change to this department's tickets — a new one at the kiosk, or
    // a colleague at a second counter calling someone — refreshes this
    // screen. Other departments' events are ignored.
    listen(['.queue.updated', '.queue.next', '.queue.call'], (ticket) => {
        if (String(ticket.service_id) === serviceId) refreshAll();
    });

    onReconnect(refreshAll);
    poll(refreshAll, 10000);

    loadSnapshot();
});
