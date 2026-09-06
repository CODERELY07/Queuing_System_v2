# Kiosk

The public, unauthenticated ticket machine. This is the only way a ticket enters the system.

- **Routes**: `GET /kiosk` (the service picker), `POST /kiosk` (issue a ticket)
- **Controller**: `App\Http\Controllers\ClientQueueController`
- **Request**: `App\Http\Requests\StoreClientQueueRequest`
- **Service class**: `App\Services\ClientQueueService`
- **Views**: `resources/views/kiosk/index.blade.php` (picker), `resources/views/kiosk/ticket.blade.php` (the issued ticket)

## Features

- **Service picker** — lists every public-facing department (`Service::publicFacing()`, i.e. everything except the internal "Admin" service — see [database.md](database.md#the-internal-admin-service)). Both the picker and the ticket-creation validation (`StoreClientQueueRequest`) enforce this independently, so it holds whether a visitor arrives via the home page or the kiosk URL directly.
- **Name + department, no account needed** — a visitor enters their name and picks a department. No login, no session, no visitor account exists anywhere in this system.
- **Priority lane** — an optional checkbox flags the ticket `priority = true`. Priority tickets are called before regular ones regardless of when they arrived (enforced in `ClientQueues::scopeNextInLine()`, consumed by [staff-dashboard.md](staff-dashboard.md)).
- **Estimated wait time** — a rough `queue_number * 2` minutes, shown on the ticket screen. This is not based on real service-time data; it's a simple placeholder.

## Validation (`StoreClientQueueRequest`)

| Field | Rule |
|---|---|
| `name` | required, string, max 255, **unique across all tickets ever issued** (`unique:client_queues,name`) |
| `service_id` | required, integer, must exist in `services` **and** have `is_internal = false` |
| `priority` | optional, boolean |

`authorize()` returns `true` unconditionally — this is the one Form Request in the app with no role restriction, since anyone walking up to the kiosk is allowed to use it.

**A note on the uniqueness rule**: `name` is unique across the *entire* `client_queues` table, not per-day or per-department. Two different visitors named "Juan Dela Cruz" on different days would collide — the second one gets a validation error, not a new ticket, unless the first ticket has since been archived (soft-deleted) or permanently removed. This is existing, unchanged behavior; if it becomes a real-world problem, the fix would be to validate uniqueness scoped to `whereNull('deleted_at')` and a `created_at` window rather than globally.

## Ticket numbering

`ClientQueueService::createTicket()`:

```php
DB::transaction(function () use ($data, &$queue) {
    $last = ClientQueues::withTrashed()
        ->where('service_id', $data['service_id'])
        ->lockForUpdate()
        ->max('queue_number');

    $queue = ClientQueues::create([
        'name' => $data['name'],
        'service_id' => $data['service_id'],
        'queue_number' => $last ? $last + 1 : 1,
        'priority' => $data['priority'] ?? false,
    ]);
});
```

Two things worth understanding:

1. **`lockForUpdate()` inside a transaction** prevents two visitors registering for the same department at the exact same moment from both computing the same "next" number and colliding on the database's `(service_id, queue_number)` unique constraint.
2. **`withTrashed()` is load-bearing, not decorative.** Soft-deleting a ticket (see [admin-queues.md](admin-queues.md)) doesn't remove its row, and the unique constraint doesn't know or care about `deleted_at`. Without `withTrashed()` here, archiving ticket `R-005` would make `max('queue_number')` return `4` again, and the very next Registration visitor would be issued `R-005` a second time — an immediate constraint violation (a 500 error at the kiosk). Numbers are issued once, ever, per department, and never reused.

The formatted ticket number shown to visitors (`R-001`, not `1`) is `ClientQueues::formatNumber($queueNumber, $prefix)` — zero-padded to 3 digits, falling back to prefix `Q` if the department has none.
