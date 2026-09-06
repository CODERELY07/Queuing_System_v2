# Admin — Queues

Search, sort, and manage every ticket ever issued.

- **Routes**: see table below
- **Controller**: `App\Http\Controllers\AdminQueueController`
- **Service class**: `App\Services\QueueArchiveService` (everything except the plain index/destroy resource actions), `App\Services\QueueStatsService` (the stat cards)
- **Views**: `resources/views/admin/queues.blade.php`, `resources/views/admin/queues-archive.blade.php`
- **Access**: `user_type:admin` only

| Route | Action |
|---|---|
| `GET /admin/queues` | The active queues list |
| `DELETE /admin/queues/{queue}` | Delete one ticket (soft) |
| `DELETE /admin/delete-old` | Bulk-delete every ticket from before today (soft) |
| `GET /admin/queues-archive` | Browse soft-deleted tickets |
| `POST /admin/queues/{id}/restore` | Un-delete an archived ticket |
| `DELETE /admin/queues/{id}/purge` | Permanently erase an archived ticket |

## Features

- **Search** — by name (partial match) or exact ticket number, via the topbar search box (`?q=`).
- **Sort** — by queue number, name, status, or created date (click a column header). Restricted to a small allow-list of columns server-side (`ClientQueues::scopeSortBy()`) so the `sort=`/`dir=` query string can never reach raw SQL.
- **Pagination** — 5 per page on the active list, 10 per page in the archive.
- **Stat cards** — Awaiting / No-shows / Finished, scoped to today (`QueueStatsService::todayCounts()`).
- **Delete one ticket** — blocked for tickets created *today* ("This Queue is New"). Otherwise soft-deleted — see below.
- **Delete Old Queues** — bulk soft-deletes every ticket dated before today, in one click, with a confirmation modal.
- **View Archived** — a badge next to the button shows the current archive count; links to the archive page.

## The archive

Everything soft-deleted (individually or via "Delete Old Queues") lands here, sortable/browsable the same way as the active list, with two actions per row:

- **Restore** — brings the ticket back onto the active list, unchanged otherwise (same status, same ticket number).
- **Delete Permanently** — the one truly irreversible action in the whole admin panel, and it's reachable *only* from the archive, never from the active list. The confirmation modal says so explicitly: *"...will be erased for good, including from analytics. This can't be undone."*

## Soft deletes and the archive

Before soft deletes existed, both "Delete Old Queues" and the per-ticket delete were permanent — a misclick had no way back, and worse, it quietly erased the rows the admin dashboard's analytics depend on (see [admin-dashboard.md](admin-dashboard.md)). `ClientQueues` now uses Eloquent's `SoftDeletes`, and:

- The active list (`AdminQueueController::index()`), the staff dashboard, and the display boards all use Eloquent's default behavior, which **excludes** trashed rows automatically — nothing extra needed there.
- The dashboard analytics (`QueueStatsService::weeklyTrend()`, `::busiestServiceToday()`) deliberately call `withTrashed()` — the whole point of soft-deleting is that clearing the active list doesn't erase history.
- Ticket numbering (`ClientQueueService::createTicket()`) also calls `withTrashed()`, for a different reason: the `(service_id, queue_number)` unique constraint doesn't know about `deleted_at`, so a "freed up" number is still occupied by the trashed row. See [kiosk.md](kiosk.md#ticket-numbering).

## What's *not* extracted into the service

`AdminQueueController::destroy()` (the one true `Route::resource` action left with real logic — only `index`/`destroy` are registered as resource routes; the others are custom, see [architecture.md](architecture.md)) keeps its same-day guard inline:

```php
if ($queue->created_at->isSameDay(now())) {
    return redirect()->back()->with('error', '...This Queue is New.');
}
```

This is a deliberate exception to "non-resource logic goes in a service" — it's a single `if` inside an actual resource action, and extracting it would add indirection with no reuse benefit.
