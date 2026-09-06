# MedQueue Documentation

This is the full reference for every module in MedQueue. Each page covers what the module does, who can use it, the routes/controllers/services behind it, and the business rules that aren't obvious just from reading the code.

Start with **[Architecture](architecture.md)** if you're new to the codebase — it explains how the app is layered (Controllers / Services / Requests / Models) and why, which every other page assumes you already know.

## Reference

| Doc | Covers |
|---|---|
| [Architecture](architecture.md) | Layering conventions, directory structure, design decisions |
| [Database Schema](database.md) | Every table, its columns, and how they relate |
| [Authentication & Roles](authentication.md) | Login, `user_type`, and how admin/staff access is enforced |
| [Real-time Broadcasting](realtime.md) | Reverb/Echo setup, the three broadcast events, what listens for them |

## Modules

| Doc | Who uses it | Covers |
|---|---|---|
| [Kiosk](kiosk.md) | Visitors | Pulling a ticket, priority lane, ticket numbering |
| [Staff Dashboard](staff-dashboard.md) | Staff | Calling/skipping/recalling patients, the live counter view |
| [Public Display Boards](display-boards.md) | Waiting-room screens | All-services board, per-department board, voice announcements |
| [Admin — Queues](admin-queues.md) | Admins | Searching/sorting tickets, bulk cleanup, the archive (soft delete/restore/purge) |
| [Admin — Staff Management](admin-staff.md) | Admins | CRUD on staff accounts |
| [Admin — Service Management](admin-services.md) | Admins | CRUD on departments, the internal "Admin" service, delete guards |
| [Admin — Dashboard & Analytics](admin-dashboard.md) | Admins | Live stat cards, the 7-day trend chart, busiest service |

## Conventions used throughout these docs

- **Route paths** are shown without the method for brevity unless it matters (e.g. `POST /staff/skip`). Check `routes/web.php` for the exact verb and middleware if you need it.
- **"Service" is overloaded** in this codebase: a `Service` model is a *department* (Registration, Pharmacy, ...), not a `App\Services\*` class. These docs always say "department" for the model and "service class" for the code layer to keep the two apart.
- Code references use the format `path/to/File.php:123` — line numbers may drift as the code evolves; the file is the durable part.
