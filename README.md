# MedQueue

MedQueue is a real-time queuing system for clinics and service counters. A visitor pulls a ticket from a kiosk, staff call patients in from a live dashboard, admins manage queues/staff/departments from a panel, and a public display screen shows who's being served — all synced live over WebSockets.

For a deeper look at any part of the system, see the **[full documentation](docs/README.md)**.

## Features

- **Kiosk ticketing** — a visitor picks a service and gets a numbered ticket, no login required. A priority lane flags senior citizens, PWDs, and pregnant visitors to be called ahead of the regular line.
- **Staff dashboard** — call the next patient, recall the previous one, mark a no-show, re-announce the current ticket, or jump to a specific ticket out of order.
- **Public display boards** — an all-services waiting-room screen, plus a dedicated full-screen display per department, both with a spoken voice announcement when a ticket is called.
- **Admin — Queues** — search/sort/paginate every ticket, soft-delete old ones in bulk or one at a time, and browse/restore/permanently delete from an archive.
- **Admin — Staff** — full CRUD on staff accounts, each tied to one service.
- **Admin — Services** — full CRUD on departments (name, ticket prefix), with guards against deleting one that still has staff or ticket history.
- **Admin — Dashboard & analytics** — active queues, active staff, today's totals, a 7-day finished/no-show trend, and the busiest service.
- **Live updates** — queue state broadcasts to every connected screen in real time via Laravel Reverb + Echo.
- **Role-based access** — a single `/dashboard` route routes users by `user_type`; `/admin/*` and `/staff/*` are each restricted to their own role.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Real-time | Laravel Reverb + Laravel Echo |
| Auth | Laravel Breeze |
| Frontend | Blade, Alpine.js, Tailwind CSS |
| Build tool | Vite |
| Database | MySQL |
| Testing | Pest |

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/CODERELY07/Queuing_System_v2.git
cd Queuing_System_v2
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Configure environment

```bash
cp .env.example .env
```

Then update `.env` with your:

- Database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Reverb configuration — run `php artisan reverb:install` if you haven't set one up yet

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Run migrations and seed the database

```bash
php artisan migrate --seed
```

This seeds four real departments (Registration, Doctor Consultation, Pharmacy, Emergency) plus one internal "Admin" service that only exists to satisfy admin accounts' `service_id`, an admin login (`admin@gmail.com` / `admin12345`), and one staff login per department (e.g. `registration@medqueue.test` / `staff12345`). See [database.md](docs/database.md#seed-data) for the full list.

### 7. Start the app

Run each in its own terminal:

```bash
php artisan serve        # Laravel app
php artisan reverb:start # WebSocket server
npm run dev              # Vite dev server
```

The app will be available at `http://localhost:8000`.

## Project Structure

```
app/
  Events/            Broadcast events (QueueCallEvent, QueueNextEvent, QueuePrevEvent)
  Http/
    Controllers/     Thin controllers — resource actions + HTTP glue only
    Middleware/       EnsureUserType (role guard), UpdateLastSeen (staff activity)
    Requests/        Form Requests — all validation lives here, grouped under Admin/ and Auth/
  Models/            ClientQueues, Service, User
  Services/          Business logic that isn't a plain CRUD resource action
resources/
  css/, js/          Tailwind entry + Alpine-driven page scripts (queuing.js, staff.js, print.js)
  views/             Blade templates, organized by area (admin/, staff/, kiosk/, display/, auth/)
routes/
  web.php            Every route in the app (no api.php — this is a server-rendered app)
docs/                Module-by-module documentation (see docs/README.md)
```

See [architecture.md](docs/architecture.md) for why the code is laid out this way.

## Key Routes

| Route | Purpose |
|---|---|
| `/` | Home / landing page |
| `/kiosk` | Client-facing kiosk to pull a queue ticket |
| `/display` | Public all-services waiting-room display |
| `/display/{service}` | Public display for one department |
| `/dashboard` | Role-based dashboard redirect |
| `/dashboard/staff` | Staff queue-control dashboard |
| `/dashboard/admin` | Admin dashboard + analytics |
| `/staff/*` | Staff-only endpoints for calling/skipping/recalling patients |
| `/admin/queues` | Admin queue management |
| `/admin/queues-archive` | Browse/restore/purge archived (soft-deleted) tickets |
| `/admin/staff` | Admin staff account management |
| `/admin/services` | Admin department management |

## Testing

```bash
composer test
```

## Documentation

Full module-by-module documentation, including every feature and the business rules behind it, lives in [`docs/`](docs/README.md):

- [Architecture](docs/architecture.md)
- [Database Schema](docs/database.md)
- [Authentication & Roles](docs/authentication.md)
- [Kiosk](docs/kiosk.md)
- [Staff Dashboard](docs/staff-dashboard.md)
- [Public Display Boards](docs/display-boards.md)
- [Admin — Queues](docs/admin-queues.md)
- [Admin — Staff Management](docs/admin-staff.md)
- [Admin — Service Management](docs/admin-services.md)
- [Admin — Dashboard & Analytics](docs/admin-dashboard.md)
- [Real-time Broadcasting](docs/realtime.md)

## License

This project is proprietary. All rights reserved.
