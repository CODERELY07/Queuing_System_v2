# Database Schema

Three tables carry the whole app: `services`, `users`, and `client_queues`. Everything else (`sessions`, `cache`, `jobs`, `password_reset_tokens`) is Laravel's own scaffolding.

## `services`

A department (Registration, Pharmacy, ...) — or the one internal "Admin" row.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | |
| `name` | string, unique | Shown everywhere in the UI. Editable via [Admin — Service Management](admin-services.md) |
| `slug` | string, unique, nullable | Powers `/display/{slug}`. Auto-generated from `name` on create, and **regenerated automatically whenever `name` changes** (`Service::uniqueSlug()`, `app/Models/Service.php`) so a renamed department's display link doesn't silently break |
| `prefix` | string(5) | The letters on a ticket, e.g. `R` → `R-001`. Uppercased on save |
| `is_internal` | boolean, default `false` | `true` only for the seeded "Admin" row. See below |
| `created_at`, `updated_at` | timestamp | |

### The internal "Admin" service

Every `User` needs a `service_id` (it's a required foreign key), but admin accounts don't belong to a real department. One `services` row named "Admin" exists solely to satisfy that constraint.

Four places need to hide this row from the public (the kiosk homepage, both display boards): they all query `Service::publicFacing()` (`->where('is_internal', false)`), **not** a check against the name. This matters because the name is now editable — before `is_internal` existed, these checks matched the literal string `"Admin"`, and renaming that row through the admin UI would have made it reappear as a bookable department. See [architecture.md](architecture.md) and `app/Models/Service.php`.

`AdminServiceController::destroy()` also refuses to delete a row with `is_internal = true`, regardless of whether it currently has staff assigned.

## `users`

An admin or staff login.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | |
| `name` | string | Unique across **all** users, admin or staff |
| `email` | string, unique | |
| `user_type` | enum('admin', 'staff') | Determines routing (`/dashboard/{user_type}`) and which routes the account can reach — see [authentication.md](authentication.md) |
| `service_id` | bigint, FK → `services.id`, `onDelete: cascade` | The department a staff member works at, or the internal "Admin" row for admins. **The cascade is why `AdminServiceController` blocks deleting a service that still has any user on it** — MySQL would otherwise silently delete those accounts |
| `email_verified_at` | timestamp, nullable | Unused in practice — `User` doesn't implement `MustVerifyEmail`, so `verified` middleware never blocks anyone |
| `password` | string, hashed | |
| `remember_token` | string, nullable | |
| `last_seen` | timestamp, nullable | Stamped by `UpdateLastSeen` middleware on every authenticated request. Drives the admin dashboard's "Staff Active" count (`last_seen >= now() - 5 minutes`) |

## `client_queues`

One ticket.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | |
| `name` | string | The visitor's name, entered at the kiosk. **Globally unique** — see [kiosk.md](kiosk.md) for what that means in practice |
| `status` | enum('waiting', 'serving', 'finish', 'skipped') | See the state machine below |
| `priority` | boolean, default `false` | Set at the kiosk. Priority tickets are called before regular ones regardless of arrival order |
| `service_id` | bigint, FK → `services.id`, `onDelete: cascade` | Which department this ticket belongs to |
| `queue_number` | unsigned int | Sequential **per department**, never reused, never reset — see [kiosk.md](kiosk.md#ticket-numbering) |
| `created_at`, `updated_at` | timestamp | `updated_at` doubles as "when this ticket's status last changed" — the admin dashboard's average-turnaround stat is `TIMESTAMPDIFF(MINUTE, created_at, updated_at)` for tickets finished today |
| `deleted_at` | timestamp, nullable | Soft delete — see below |

Unique constraint: `(service_id, queue_number)`.

### Status state machine

```
                 ┌──────────┐
   kiosk create  │ waiting  │
   ─────────────▶│          │
                 └────┬─────┘
                      │ Next Patient (priority first, then oldest)
                      ▼
                 ┌──────────┐   No-show    ┌─────────┐
                 │ serving  │─────────────▶│ skipped │
                 └────┬─────┘              └────┬────┘
                      │ Next Patient              │
                      ▼                           │ Previous Patient (recall)
                 ┌──────────┐                      │
                 │  finish  │◀─────────────────────┘
                 └──────────┘        (also recallable back to `serving`)
```

- **waiting → serving**: "Next Patient" (priority lane first, then oldest `queue_number`) or a staff member picking a specific row ("Call").
- **serving → finish**: "Next Patient" finishes whoever's currently serving before calling the next one.
- **serving → skipped**: "No-show".
- **finish or skipped → serving**: "Previous Patient" — recalls whichever ticket most recently left the `serving` state, whether it finished normally or was a no-show (see [staff-dashboard.md](staff-dashboard.md)).

All of this logic lives in `App\Services\QueueCallService`, not the controller — see [architecture.md](architecture.md).

### Soft deletes

`deleted_at` is set by:
- **Admin — Delete Old Queues** (bulk: everything before today)
- **Admin — per-ticket Delete** on the queues page

and cleared by **Restore** on the [archive page](admin-queues.md#the-archive). The only way to truly erase a row is **Delete Permanently**, from the archive only.

This exists so the dashboard's historical numbers (7-day trend, busiest department, finished/no-show totals) don't lose data every time an admin tidies up the active list — those queries deliberately use `withTrashed()`. See [admin-queues.md](admin-queues.md#soft-deletes-and-the-archive) and [admin-dashboard.md](admin-dashboard.md).

## Seed data

`database/seeders/DatabaseSeeder.php` runs `ServiceSeeder` then `UserSeeder`:

| Service | Prefix | `is_internal` |
|---|---|---|
| Registration | R | no |
| Doctor Consultation | D | no |
| Pharmacy | P | no |
| Emergency | E | no |
| Admin | A | **yes** |

| Login | Password | Role | Department |
|---|---|---|---|
| `admin@medqueue.test` | `admin12345` | admin | Admin (internal) |
| `registration@medqueue.test` | `staff12345` | staff | Registration |
| `doctor@medqueue.test` | `staff12345` | staff | Doctor Consultation |
| `pharmacy@medqueue.test` | `staff12345` | staff | Pharmacy |
| `emergency@medqueue.test` | `staff12345` | staff | Emergency |

Both seeders use `updateOrCreate`/`firstOrCreate`, so `php artisan migrate --seed` is safe to re-run against an existing database.
