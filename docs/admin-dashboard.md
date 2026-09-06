# Admin — Dashboard & Analytics

The admin's landing page after login (`/dashboard/admin`).

- **Route**: `GET /dashboard/{user_type}` → `UserController::index()` (the same router used for the staff dashboard — see [authentication.md](authentication.md#routing-by-role))
- **Service class**: `App\Services\QueueStatsService`
- **View**: `resources/views/admin/dashboard.blade.php`
- **Access**: `user_type:admin` only

## Features

### Top row — live operational stats

| Card | Source |
|---|---|
| Active Queues | Count of tickets with `status = serving`, system-wide |
| Staff Active | Count of staff accounts with `last_seen` within the last 5 minutes (see [authentication.md](authentication.md#staff-activity-tracking)) |
| Priority Waiting | Count of `status = waiting AND priority = true` tickets, system-wide |

### "Queueing Analytics" — historical numbers

| Card / chart | Source (`QueueStatsService`) |
|---|---|
| Tickets Today / Finished Today / No-shows Today | `todayCounts()` |
| Avg. Turnaround | `avgTurnaroundMinutesToday()` — average `TIMESTAMPDIFF(MINUTE, created_at, updated_at)` across today's finished tickets. A proxy for "time in the building," not literally "time spent being served," since there's no separate "serving started" timestamp on the row |
| Last 7 days (chart) | `weeklyTrend()` — finished vs. no-show counts per day, today included |
| Busiest Service Today | `busiestServiceToday()` — the department with the most tickets today |

## Why the history queries read `withTrashed()`

`weeklyTrend()` and `busiestServiceToday()` deliberately include soft-deleted tickets. This is the entire reason soft deletes exist on `ClientQueues` in the first place (see [admin-queues.md](admin-queues.md#soft-deletes-and-the-archive)): an admin clicking "Delete Old Queues" to tidy up the active list should never make yesterday's numbers disappear from this chart. `todayCounts()` (used for the "Tickets Today" row) doesn't need `withTrashed()` — "Delete Old Queues" only ever touches tickets from *before* today, so today's own counts are never affected either way.

## One source of truth for "today's counts"

The exact same waiting/skipped/finished-today counts are needed in three places: this dashboard, the [admin queues page](admin-queues.md)'s stat cards, and the [staff dashboard](staff-dashboard.md)'s stat cards (scoped to one department). All three call `QueueStatsService::todayCounts(?int $serviceId = null)` — before this was extracted, the same three-line query block was hand-copied in each controller with slightly different scoping each time.
