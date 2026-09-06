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

## How the dashboard stays in sync

Since there's normally exactly one staff account per department, the dashboard doesn't need to react to changes made elsewhere — every update on screen is a direct result of an action *this* staff member just took. The one exception is the ticket table's row statuses: those come from the server-rendered page and only update on the next full page load (pagination, search, sort) or after clicking a row's own "Call" button, which triggers a full reload on success.
