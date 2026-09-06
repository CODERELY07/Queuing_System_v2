# Architecture

MedQueue is a server-rendered Laravel app (Blade + Alpine.js, no SPA framework, no `routes/api.php`) with one real-time layer (Laravel Reverb) bolted on for the parts that need to update without a page reload: the staff dashboard's live counter and the public display boards.

## Layering

The codebase follows one rule consistently: **controllers hold resource actions, services hold everything else, requests hold validation.**

```
HTTP request
   │
   ▼
Form Request (app/Http/Requests/**)  ── validates input, and (for admin actions) authorizes the role
   │
   ▼
Controller (app/Http/Controllers/**) ── resource verbs only: index/show/store/update/destroy,
   │                                     plus thin glue for anything else (call a service, shape the response)
   ▼
Service (app/Services/**)            ── the actual business logic: state transitions, aggregation,
   │                                     numbering, anything that isn't "map this input onto a model"
   ▼
Model (app/Models/**)                ── Eloquent models, scopes, relationships, the few model-level
                                          hooks that must run on every save regardless of caller (e.g.
                                          Service's slug regeneration)
```

### Why split it this way

A controller method that does validation, a business rule, and persistence all inline is hard to reuse and hard to test — you can't call "cancel a ticket" from a queued job or a console command without going through HTTP. Splitting it means:

- **Form Requests** can be unit-tested for validation rules alone, and Laravel's own exception handling turns a failure into a proper structured `422 {errors: {...}}` response automatically — no `try/catch` needed around them.
- **Services** are plain PHP classes with no HTTP awareness (no `$request`, no `response()->json()`). They take arguments, return data or throw, and can be called from a controller, a command, a test, or another service.
- **Controllers** stay short enough to read in one screen, which makes it obvious at a glance whether an endpoint is missing a validation step or an authorization check.

### What counts as "not a resource action"

`Route::resource` maps a URL to one of seven verbs (index/create/store/show/edit/update/destroy). Anything outside that shape — `POST /staff/call-next`, `DELETE /admin/delete-old`, `GET /admin/queues-archive` — is custom business logic wearing a route, and its logic lives in a service:

| Custom action | Service |
|---|---|
| Call next / previous / skip / recall / call a specific ticket | `QueueCallService` |
| Bulk-delete old queues, browse/restore/purge the archive | `QueueArchiveService` |
| Kiosk ticket numbering | `ClientQueueService` |
| Waiting-room board formatting (all-services + per-department) | `DisplayBoardService` |
| Dashboard stat aggregation (today's counts, 7-day trend, busiest department) | `QueueStatsService` |

The one deliberate exception: `AdminQueueController::destroy()` — a real resource action — keeps its one business rule ("you can't delete a ticket created today") inline, since it's a single `if` and moving it out would just add an indirection with no reuse benefit.

## Directory structure

```
app/
  Events/
    QueueCallEvent.php     Broadcasts "this ticket was just called" (public channel `queue`, event `.queue.call`)
    QueueNextEvent.php     Broadcasts "queue state changed" (`.queue.next`) — after Next Patient or a no-show
    QueuePrevEvent.php     Broadcasts on "Previous Patient" (`.queue.prev`) — currently unused by any listener
  Http/
    Controllers/
      Auth/                Laravel Breeze's stock login/register/password/verification controllers
      AdminQueueController.php     Resource: index/destroy on client_queues, + non-resource archive actions
      AdminServiceController.php   Resource: index/store/update/destroy on services
      AdminStaffController.php     Resource: index/store/update/destroy on staff (User where user_type=staff)
      ClientQueueController.php    Kiosk: index/store
      DisplayAllQueueController.php Public display boards
      ProfileController.php        Breeze's account settings page
      StaffController.php          Staff's non-resource queue-control actions
      UserController.php           The single /dashboard/{user_type} router
    Middleware/
      EnsureUserType.php   Route middleware: `user_type:admin` / `user_type:staff`
      UpdateLastSeen.php   Appended to the global `web` group; stamps last_seen on every authenticated request
    Requests/
      Admin/               StoreStaffRequest, UpdateStaffRequest, StoreServiceRequest, UpdateServiceRequest
      Auth/LoginRequest.php  (Breeze)
      ProfileUpdateRequest.php  (Breeze)
      StoreClientQueueRequest.php
  Models/
    ClientQueues.php       A single ticket. Soft-deletable. See database.md
    Service.php            A department (or the internal "Admin" bucket). See database.md
    User.php               Admin or staff account
  Services/
    ClientQueueService.php
    DisplayBoardService.php
    QueueArchiveService.php
    QueueCallService.php
    QueueStatsService.php
  Providers/AppServiceProvider.php   Empty — no custom bindings needed; every service above is a plain
                                      concrete class Laravel's container resolves automatically
resources/
  views/
    admin/                 Admin panel pages (dashboard, queues, queues-archive, staff, services, index)
    staff/dashboard.blade.php
    kiosk/                 index (service picker) + ticket (the printed/on-screen ticket)
    display/               counter (all-services board) + show (one department's board)
    partials/admin/        Reusable table/modal partials shared by the staff and services CRUD pages
    components/            Design-system pieces (stat-card, status-badge, icon.*, modal, ...)
  js/
    queuing.js             Staff dashboard actions + both display boards + voice announcements
    staff.js                Generic AJAX form handler for the admin Staff/Services modals
    print.js, echo.js       Ticket printing, Echo/Reverb client setup
routes/
  web.php                  Every route — there is no routes/api.php
docs/                      You are here
```

## Roles and route protection

There are exactly two `user_type` values: `admin` and `staff`. Both need `auth` + `verified` (the latter is a no-op today — see [authentication.md](authentication.md)) plus one of:

- `user_type:admin` on the whole `/admin/*` group
- `user_type:staff` on the whole `/staff/*` group

`EnsureUserType` (`app/Http/Middleware/EnsureUserType.php`) is a single reusable middleware for both — `abort(403)` if the authenticated user's `user_type` isn't in the allowed list. Every Admin Form Request also re-asserts `$this->user()?->user_type === 'admin'` in its own `authorize()`, which is redundant with the route middleware today but is the correct place for a Form Request to state who may submit it, independent of how the route happens to be wired.

## Data retention: soft deletes

`ClientQueues` uses Eloquent's `SoftDeletes`. This exists specifically so the admin dashboard's analytics (today's totals, the 7-day trend, busiest department) stay accurate even after old tickets are cleared out of the active list — see [admin-queues.md](admin-queues.md#soft-deletes-and-the-archive) for the full reasoning and [admin-dashboard.md](admin-dashboard.md) for how the analytics queries deliberately read `withTrashed()`.

One consequence worth knowing if you touch ticket numbering: `(service_id, queue_number)` is uniquely constrained at the database level, and that constraint doesn't care about `deleted_at`. `ClientQueueService::createTicket()` computes the next number with `withTrashed()->max('queue_number')` for exactly this reason — see [kiosk.md](kiosk.md#ticket-numbering).
