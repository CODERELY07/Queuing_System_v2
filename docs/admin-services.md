# Admin — Service Management

Full CRUD on departments — what used to be seed-only data (`database/seeders/ServiceSeeder.php`) is now editable from the admin panel.

- **Routes**: `GET /admin/services`, `POST /admin/services`, `PUT /admin/services/{id}`, `DELETE /admin/services/{id}`
- **Controller**: `App\Http\Controllers\AdminServiceController`
- **Requests**: `App\Http\Requests\Admin\StoreServiceRequest`, `App\Http\Requests\Admin\UpdateServiceRequest`
- **Views**: `resources/views/admin/services.blade.php`, `resources/views/partials/admin/service-table.blade.php`, `resources/views/partials/admin/service-modal.blade.php`
- **Frontend**: `resources/js/staff.js` (same generic AJAX handler as [Staff Management](admin-staff.md))
- **Access**: `user_type:admin` only

## Features

- **List** — every department (including the internal "Admin" one — this page is the one place in the admin panel that intentionally shows it, so an admin can see it has staff attached and understand why it can't be deleted), with a live staff count and ticket count per row.
- **Add** — name + ticket prefix (auto-uppercased, e.g. `r` → `R`). The `/display/{slug}` URL is generated automatically from the name.
- **Edit** — same two fields. Renaming a department **regenerates its slug** automatically (`Service::uniqueSlug()`, in a model `updating` hook) so its `/display/{slug}` link follows the new name instead of quietly pointing at stale text.
- **Delete** — blocked (with a clear reason shown before you even try) if the department is internal, still has staff assigned, or still has queue history — see below.

## Validation

| Field | Create (`StoreServiceRequest`) | Update (`UpdateServiceRequest`) |
|---|---|---|
| `name` | required, string, max 100, unique | same, ignoring this record's own current value |
| `prefix` | required, string, max 5, unique | same, ignoring this record |

## Why deletion is guarded three ways

Both `services.service_id` foreign keys — from `users` and from `client_queues` — are `onDelete: cascade`. Deleting a `services` row with either still pointing at it wouldn't fail; MySQL would silently delete the dependent rows too. That's catastrophic here specifically: it would delete staff **logins**, and it would permanently erase **queue history that the admin dashboard's analytics depend on** (see [admin-dashboard.md](admin-dashboard.md)) — including soft-deleted/archived tickets, which still count for that history and are just as cascade-vulnerable.

`AdminServiceController::destroy()` checks, in order:

1. **Is it the internal service?** (`is_internal`) — refused unconditionally, regardless of the checks below.
2. **Does it have any staff?** (`users_count`, via `withCount('users')`) — refused with "Reassign or remove this service's staff before deleting it."
3. **Does it have any tickets, including archived ones?** (`tickets_count`, via `withCount(['clientQueues as tickets_count' => fn ($q) => $q->withTrashed()])`) — refused with "...its records are kept for reporting."

The confirm-delete modal computes the same two counts up front and shows the specific blockers (*"staff member: 1, queue ticket: 4"*) before the admin even clicks Delete, rather than only finding out after a failed request.

## The internal "Admin" service and renaming

See [database.md](database.md#the-internal-admin-service) for the full story. The short version: four other parts of the app (the kiosk homepage, the kiosk picker, both display boards) used to identify this one row by matching the literal string `"Admin"` — which broke the moment this CRUD page made the name editable. They now all check `is_internal` instead, so **renaming the internal service through this page is safe** and won't make it reappear anywhere public.
