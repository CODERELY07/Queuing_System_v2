# Real-time Broadcasting

MedQueue uses Laravel Reverb (a self-hosted WebSocket server) with Laravel Echo on the frontend. There's exactly one public broadcast channel — `queue` — and three events on it.

## Setup

Reverb must be running alongside the app (`php artisan reverb:start`) — see the root [README](../README.md#7-start-the-app). `BROADCAST_CONNECTION=reverb` in `.env` selects it. `resources/js/echo.js` configures the Echo client; it's imported once via `app.js` and available globally as `window.Echo`.

The `queue` channel is a plain public `Channel`, not a `PrivateChannel` — there's no entry in `routes/channels.php` for it, and none is needed, since nothing broadcast on it is sensitive (ticket numbers and first names, already shown on a physical waiting-room screen).

## The three events

All three extend the abstract `QueueTicketEvent` (`ShouldBroadcastNow`, so no queue worker is needed) and broadcast `{id, status, service_id, queue_number}`. Only `QueueCallEvent` adds `name`, because the voice announcement needs it; nothing else on the public channel carries a visitor's name. Services dispatch them through `BroadcastsSafely::broadcastSafely()`, so an unreachable Reverb server is logged instead of failing the staff action.

| Event | Broadcast name | Fired by | Fired when |
|---|---|---|---|
| `QueueCallEvent` | `.queue.call` | `QueueCallService::recall()`, `::callSelected()` | A ticket is called or re-announced — "Recall", or picking a specific ticket off the table |
| `QueueNextEvent` | `.queue.next` | `QueueCallService::callNext()`, `::callPrevious()`, `::skip()` | Queue state changed in a way that isn't a fresh "call" — advancing to the next patient, undoing to the previous one, or a no-show |
| `QueueUpdatedEvent` | `.queue.updated` | `ClientQueueService::createTicket()` | A new ticket was issued at the kiosk |

`app/Events/` — `QueueTicketEvent` (base) plus one small subclass per event, each only setting its `broadcastAs()` name.

## What listens, and where

Listeners use `resources/js/lib/realtime.js` (`listen`, `onReconnect`, `poll`, `isConnected`), which also drives the on-screen "Live / Reconnecting" indicator.

```js
// resources/js/display.js: both display boards
listen(['.queue.call'], speak);                    // voice: "{name}, ticket number {n}, please come in"
listen(['.queue.call', '.queue.next'], reload);    // re-fetch the serving list / single-department panel
onReconnect(reload);                               // events missed while offline
poll(reload);                                      // fallback refresh while the socket is down

// resources/js/counter.js: staff dashboard
listen(['.queue.updated', '.queue.next', '.queue.call'], refreshAll);
onReconnect(refreshAll);
poll(refreshAll, 10000);
```

Every listener **re-fetches from the server** rather than trusting the event payload to build the new UI state; the event is just a "something changed, go check" signal. Firing an event more than once for the same change is therefore only wasted traffic, never a correctness bug.

### The staff dashboard's filter

`counter.js` only runs when `#staff-dashboard-meta` exists, and ignores an event whose `service_id` differs from that element's `data-service-id`. Every department broadcasts on the same public `queue` channel, so without this check Pharmacy's counter would refresh every time Registration issued a ticket.

`refreshAll()` doesn't reload the page: it re-fetches the dashboard data and swaps in just the `#queue-table-panel` element, so scroll position and text typed into the search box survive. While an action request is in flight the buttons are disabled (`setBusy`) to prevent double submits.

## Voice announcements

`VoiceQueue()` uses the browser's `speechSynthesis` API, not a server-side TTS service — the announcement only plays on whichever physical screen is showing the display board, which is the point (the waiting room hears it, not staff's own machine). Voice selection prefers `"Google US English"` if available, falling back to whatever the browser offers first.
