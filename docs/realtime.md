# Real-time Broadcasting

MedQueue uses Laravel Reverb (a self-hosted WebSocket server) with Laravel Echo on the frontend. There's exactly one public broadcast channel — `queue` — and four events on it.

## Setup

Reverb must be running alongside the app (`php artisan reverb:start`) — see the root [README](../README.md#7-start-the-app). `BROADCAST_CONNECTION=reverb` in `.env` selects it. `resources/js/echo.js` configures the Echo client; it's imported once via `app.js` and available globally as `window.Echo`.

The `queue` channel is a plain public `Channel`, not a `PrivateChannel` — there's no entry in `routes/channels.php` for it, and none is needed, since nothing broadcast on it is sensitive (ticket numbers and first names, already shown on a physical waiting-room screen).

## The four events

All four implement `ShouldBroadcastNow` (synchronous — no queue worker needed) and broadcast the same shape: `{id, name, status, service_id, queue_number}`.

| Event | Broadcast name | Fired by | Fired when |
|---|---|---|---|
| `QueueCallEvent` | `.queue.call` | `QueueCallService::recall()`, `::callSelected()` | A ticket is called or re-announced — "Recall", or picking a specific ticket off the table |
| `QueueNextEvent` | `.queue.next` | `QueueCallService::callNext()`, `::callPrevious()`, `::skip()` | Queue state changed in a way that isn't a fresh "call" — advancing to the next patient, undoing to the previous one, or a no-show |
| `QueueUpdatedEvent` | `.queue.updated` | `ClientQueueService::createTicket()` | A new ticket was issued at the kiosk |
| `QueuePrevEvent` | `.queue.prev` | *(nothing currently)* | Defined but unused — `callPrevious()` fires `QueueNextEvent`, not this. Kept for now in case a future change wants to distinguish "went back" from "went forward" on the frontend |

`app/Events/*.php` — all four are near-identical; `QueueNextEvent`'s constructor also logs the payload via `Log::debug()` for local debugging.

## What listens, and where (`resources/js/queuing.js`)

```js
window.Echo.channel('queue').listen('.queue.call', (event) => {
    VoiceQueue(event);       // speaks "{name}, ticket number {queue_number}, please come in" —
                              // only on the all-services board, or a department's own board when
                              // the call is for that department
    displayQeueue(event);    // re-fetches /display/serving-patients (all-services board)
    displaySingleQueue();    // re-fetches /display/{slug}/serving-patient (single-department board)
});

window.Echo.channel('queue').listen('.queue.next', (event) => {
    displayQeueue(event);
    displaySingleQueue();
});

window.Echo.channel('queue').listen('.queue.prev', (event) => {
    displayQeueue(event);
    displaySingleQueue();
});

// The one listener the staff dashboard actually cares about — everything
// above is display-board-only.
window.Echo.channel('queue').listen('.queue.updated', (ticket) => {
    if (window.location.pathname !== '/dashboard/staff') return;

    const meta = document.getElementById('staff-dashboard-meta');
    if (!meta || String(meta.dataset.serviceId) !== String(ticket.service_id)) return;

    loadDashboardData();   // "Currently Serving" + "Up Next"
    refreshQueueTable();   // the paginated table below
});
```

Every listener **re-fetches from the server** rather than trusting the event payload to build the new UI state — the event is just a "something changed, go check" signal. This is why firing an event more than once for the same logical change (which used to happen in `callNext()` before it was refactored into `QueueCallService`) was only ever wasted network traffic, never a correctness bug: whichever fetch lands last always reflects the true current state.

### The staff dashboard's own listener

Unlike the display-board listeners, `.queue.updated` **is** filtered — every department's kiosk submissions broadcast on the same public `queue` channel, so without checking `service_id` against a `data-service-id` attribute rendered into the page (`staff-dashboard.md`'s `#staff-dashboard-meta` element), Pharmacy's dashboard would refresh every time Registration got a new ticket.

`refreshQueueTable()` doesn't reload the page — it re-fetches the *same* URL (preserving whatever page/sort/search is currently in it) and swaps in just the `#queue-table-panel` element parsed out of that response, so a new ticket shows up without losing scroll position or text mid-typed into the search box. The panel dims (`opacity-50`) while that fetch is in flight, and `bindSelectedCallButtons()` re-runs afterward since the replaced buttons are new DOM nodes with no click handlers of their own yet.

Staff still don't see each other's *actions* live (calling/skipping) — only new tickets arriving. Two staff accounts sharing one department's counter still won't see each other's calls without taking an action of their own, but in practice each department has exactly one staff account, so this hasn't been a practical problem.

## Voice announcements

`VoiceQueue()` uses the browser's `speechSynthesis` API, not a server-side TTS service — the announcement only plays on whichever physical screen is showing the display board, which is the point (the waiting room hears it, not staff's own machine). Voice selection prefers `"Google US English"` if available, falling back to whatever the browser offers first.
