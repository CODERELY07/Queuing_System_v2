# Public Display Boards

The waiting-room screens. Two flavors, both unauthenticated (meant to run full-screen on a TV, not behind a login):

- **All-services board** — every department's "currently serving" number on one screen.
- **Per-department board** — one department's own dedicated full-screen display, for mounting at that department's specific waiting area.

## Routes, controller, service

| Route | Purpose |
|---|---|
| `GET /display` | The all-services board page |
| `GET /display/serving-patients` | JSON polled/refreshed by the all-services board |
| `GET /display/{service:slug}` | One department's dedicated board page |
| `GET /display/{service:slug}/serving-patient` | JSON polled/refreshed by that department's board |

- **Controller**: `App\Http\Controllers\DisplayAllQueueController`
- **Service class**: `App\Services\DisplayBoardService`
- **Views**: `resources/views/display/counter.blade.php` (all-services), `resources/views/display/show.blade.php` (single department)
- **Frontend**: `resources/js/queuing.js` (`displayQeueue()`, `displaySingleQueue()`)

Both JSON endpoints, and both page routes, exclude the internal "Admin" service (`Service::publicFacing()` for the listing pages; `abort_if($service->is_internal, 404)` on the single-department page) — see [database.md](database.md#the-internal-admin-service).

## Features

- **Currently serving, per department** — the ticket number and visitor name of whoever's `status = serving`. Shows "No patient being served" when nobody is.
- **Up next** — up to 3 waiting tickets, **numbers only, deliberately no names** (a waiting visitor's name is never shown on a public screen, only the person already at the counter).
- **Voice announcement** — when a ticket is called, the board speaks it via the browser's `speechSynthesis` API: *"{name}, ticket number {queue\_number}, please come in."* Only plays on the all-services board, or a department's own board when the call is for that department — never announces a different department's call on a screen that isn't showing it.
- **Live updates, no polling loop** — both boards re-fetch their JSON only in response to a broadcast event (`.queue.call`, `.queue.next`, `.queue.prev` on the `queue` channel), not on a timer. See [realtime.md](realtime.md).

## Shared formatting logic (`DisplayBoardService`)

`servingPatients()` (all departments) and `servingPatient()` (one department) used to duplicate the same "shape a department's serving/next data" logic almost line-for-line. `DisplayBoardService::boardFor(Service $service)` is the single implementation both now call:

```php
[
    'service_name' => ...,
    'service_prefix' => ...,
    'serving' => $serving ? ['number' => 'R-004', 'name' => 'Juan Dela Cruz'] : null,
    'next' => ['R-005', 'R-006', 'R-007'],
]
```

It transparently handles both cases — a service with `clientQueues`/`waitingQueues` already eager-loaded (the all-services board, one query for every department via `allBoards()`) and one queried fresh (the single-department board, since Laravel's route-model binding on `Service $service` doesn't eager-load anything) — by checking `$service->relationLoaded(...)` before deciding whether to read the preloaded collection or run a scoped query.
