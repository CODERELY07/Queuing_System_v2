# Staff Dashboard

The counter-side screen a staff member keeps open all shift. One account per department (see [database.md](database.md#seed-data)); everything here is scoped to the logged-in staff member's own `service_id`.

- **Routes**: `GET /dashboard/staff` (the page), plus the action endpoints under `/staff/*` (all `POST` except `dashboard-data`)
- **Controller**: `App\Http\Controllers\StaffController`
- **Service class**: `App\Services\QueueCallService`
- **View**: `resources/views/staff/dashboard.blade.php`
- **Frontend**: `resources/js/queuing.js`
- **Access**: `user_type:staff` only — see [authentication.md](authentication.md)

## Features

| Button | Endpoint | What it does |
|---|---|---|
| **Next Patient** (or press <kbd>Space</kbd>) | `POST /staff/call-next` | Finishes whoever's currently serving (if anyone), then calls the next ticket in line — priority tickets first, then oldest by `queue_number` |
| **Previous Patient** | `POST /staff/call-previous` | Undoes the last call: puts the current ticket back to `waiting`, and brings back whichever ticket most recently left `serving` — whether it *finished* or was a *no-show* |
| **No-show** | `POST /staff/skip` | Marks the current ticket `skipped`. Rejected (`422`) if nobody's being served |
| **Recall** | `POST /staff/call` | Re-announces whoever's currently serving (fires the same voice announcement as a fresh call, without changing any status). Rejected (`422`) if nobody's being served |
| **Call** (per row, in the table) | `POST /staff/call/{id}` | Calls a specific ticket out of order — e.g. skipping the FIFO line to call a particular person. Only shown for tickets that aren't already `finish` (see below) |

The top panel — "Currently Serving", the waiting count, and "Up Next" (the next 3 tickets in line) — refreshes via `GET /staff/dashboard-data` after every action, and once on page load. It does **not** poll on an interval and does **not** listen for broadcast events — see [realtime.md](realtime.md#what-listens-and-where) for why that's a reasonable tradeoff given one staff account per department.

## Business rules worth knowing

### "Previous Patient" includes no-shows, not just finished tickets

If the very last thing that happened was a no-show, "Previous Patient" undoes *that* — it doesn't reach further back to whatever finished before it. This is `QueueCallService::callPrevious()`:

```php
$previous = ClientQueues::forService($serviceId)
    ->whereIn('status', ['finish', 'skipped'])
    ->orderBy('queue_number', 'desc')
    ->first();
```

### A finished ticket can't be re-called

Every row in the table below the top panel has a "Call" button — except finished ones, which show a `—` instead. This is enforced twice: the view hides the button (`resources/views/staff/dashboard.blade.php`, `@if ($queue->status !== 'finish')`), and `QueueCallService::callSelected()` independently rejects it (`422 "This ticket is already finished."`) even if the button were somehow clicked anyway.

### What a no-show does to the public display

Marking a ticket `skipped` broadcasts `QueueNextEvent`, which tells both display boards to re-fetch. Without this, a no-show ticket would keep showing as "currently being served" on the waiting-room screen — including to that same visitor, if they walked in late — until the next real call happened to overwrite it. See [realtime.md](realtime.md).

### Every action result feeds back into the UI, not just the console

Each button's `fetch()` call checks the response for `{success: false, message: "..."}` and shows it via `alert()` if present — e.g. clicking "No-show" with nobody being served now tells the staff member why nothing happened, rather than silently doing nothing.

### One shared "busy" lock across every action

"Next Patient", "Previous Patient", "Recall", "No-show", and every row's own "Call" button all share one `staffActionsBusy` flag (`resources/js/queuing.js`). Starting any one of them disables all the others until it resolves. This exists because none of the main four buttons used to guard against a second click landing before the first request's response came back — clicking "Next Patient" twice quickly (or "No-show" while "Next Patient" was still in flight) could fire two overlapping requests against the same ticket, which is exactly how a patient ends up silently skipped or the counter ends up in a state nobody actually clicked for. The per-row "Call" button already learned this lesson once, for a different reason — see the comment at the top of `queuing.js` about the recursion bug that predates this.

While busy, the four main buttons get `disabled` plus a dimmed/`cursor-not-allowed` treatment so it's visually obvious an action is still processing, not just functionally blocked.

## How the dashboard stays in sync

Every button's own result updates the screen immediately (see above). Beyond that, the dashboard also listens for `.queue.updated` — broadcast whenever a new ticket is issued at the kiosk (see [realtime.md](realtime.md)) — filtered to this department only, via a `data-service-id` attribute rendered into a hidden `#staff-dashboard-meta` element. On a match it refreshes both the top panel (`loadDashboardData()`) and the ticket table below (`refreshQueueTable()`, a partial fetch-and-swap of `#queue-table-panel` that preserves the current page/sort/search rather than a full reload) — so a new ticket shows up on a staff member's screen without them touching anything.

Staff still don't see each other's *actions* live — calling, skipping, recalling. Two staff accounts sharing one department's counter won't see each other's calls without taking an action of their own, but in practice each department has exactly one staff account, so this hasn't been a practical problem.

Before a first server response arrives (or on first paint before `/staff/dashboard-data` resolves), "Currently Serving" and "Up Next" both show a pulsing skeleton placeholder rather than sitting blank or saying "Loading..." — the skeleton is swapped out for real content the first time `loadDashboardData()` succeeds, and is a no-op to "swap out" again on every later refresh.
