# Adding & Changing Features

A short, practical guide: **where every existing feature lives**, and **a recipe for each kind of change** you're likely to make. For *why* the code is layered this way, see [architecture.md](architecture.md).

## 1. How a request travels

```
Browser ──▶ routes/web.php (+ role middleware)
        ──▶ FormRequest        validates + authorizes      app/Http/Requests/
        ──▶ Controller         thin: calls a service       app/Http/Controllers/
        ──▶ Service            business rules, transactions app/Services/
        ──▶ Model / database   scopes, casts, migrations   app/Models/, database/
        ──▶ Event (optional)   broadcast after commit      app/Events/
              └─▶ Reverb ──▶ Echo in the browser ──▶ page script   resources/js/
        ──▶ Blade view or JSON                              resources/views/
```

Where each kind of file goes:

| You're adding… | Put it in | Notes |
|---|---|---|
| A URL | `routes/web.php` | Pick the right group: public, `auth`, `user_type:staff`, `user_type:admin` |
| Validation / "who may do this" | `app/Http/Requests/` | `authorize()` + `rules()`; shared rules in an abstract base (see `StaffRequest`) |
| Business logic | `app/Services/` | Anything with more than one DB write, a lock, or a broadcast |
| A table or column | `database/migrations/` | New file, never edit an old one |
| A page | `resources/views/<area>/` | `admin/`, `staff/`, `kiosk/`, `display/`, `auth/` |
| A reusable UI piece | `resources/views/components/` | Used as `<x-name>`; icons in `components/icon/` |
| Page behavior | `resources/js/<page>.js` | Imported once in `resources/js/app.js` |
| A live update | `app/Events/` + `resources/js/lib/realtime.js` | |
| A test | `tests/Feature/` | Pest |

## 2. Feature map: what exists today

| Feature | URL → controller | Service / request | Models & tables | Views | JS & events |
|---|---|---|---|---|---|
| **Homepage** | `/` (closure in `routes/web.php`) | — | `Service::publicFacing()` | `home.blade.php`, `layouts/guest`, `partials/header`, `partials/footer` | — |
| **Kiosk ticketing** | `/kiosk` → `ClientQueueController` | `ClientQueueService`, `StoreClientQueueRequest` | `ClientQueues` (`client_queues`), `Service` | `kiosk/index`, `kiosk/ticket` | `print.js`; fires `QueueUpdatedEvent` |
| **Staff counter** | `/dashboard/staff` → `UserController`; `/staff/*` → `StaffController` | `QueueCallService`, `StaffQueueService`, `QueueStatsService` | `ClientQueues` | `staff/dashboard`, `staff/partials/queue-table`, `components/queue-status`, `components/queue-person` | `counter.js`; fires `QueueCallEvent`, `QueueNextEvent` |
| **All-services board** | `/display` → `DisplayAllQueueController` | `DisplayBoardService` | `ClientQueues`, `Service` | `display/counter`, `display/partials/voice-toggle` | `display.js` (listens) |
| **Department board** | `/display/{slug}` → `DisplayAllQueueController` | `DisplayBoardService` | `Service.slug` | `display/show` | `display.js` |
| **Admin dashboard** | `/dashboard/admin` → `UserController` | `QueueStatsService` | `ClientQueues`, `User.last_seen` | `admin/dashboard`, `components/stat-card` | `live-status.js` |
| **Admin queues** | `/admin/queues` → `AdminQueueController` | `QueueStatsService` | `ClientQueues` | `admin/queues`, `components/sortable-th` | — |
| **Queue archive** | `/admin/queues-archive` → `AdminQueueController` | `QueueArchiveService` | `ClientQueues` (soft deletes) | `admin/queues-archive` | — |
| **Admin staff** | `/admin/staff` → `AdminStaffController` | `StoreStaffRequest`, `UpdateStaffRequest` (base `StaffRequest`) | `User` | `admin/staff`, `partials/admin/staff-table`, `staff-modal` | `admin-forms.js` |
| **Admin services** | `/admin/services` → `AdminServiceController` | `StoreServiceRequest`, `UpdateServiceRequest` (base `ServiceRequest`) | `Service` | `admin/services`, `partials/admin/service-table`, `service-modal` | `admin-forms.js` |
| **Login & passwords** | `/login`, `/forgot-password`… → `Auth/*` (`routes/auth.php`) | `Auth/LoginRequest` | `User` | `auth/*` | — |
| **Profile** | `/profile` → `ProfileController` | `ProfileUpdateRequest` | `User` | `profile/*` | — |
| **Demo accounts** | login page | `config/demo.php`, `ProtectDemoAccounts`, `UserSeeder` | `User` | `auth/login` | — |
| **Layout & navigation** | — | `app/View/Components/AppLayout`, `GuestLayout` | — | `layouts/app`, `layouts/sidebar-nav`, `layouts/topbar`, `layouts/guest` | `app.js` |
| **Real-time** | — | `BroadcastsSafely`, `QueueTicketEvent` | — | `components/live-indicator` | `echo.js`, `lib/realtime.js`, `live-status.js`; `config/reverb.php`, `config/broadcasting.php` |

Deeper per-module notes: [kiosk](kiosk.md) · [staff dashboard](staff-dashboard.md) · [display boards](display-boards.md) · [admin queues](admin-queues.md) · [admin staff](admin-staff.md) · [admin services](admin-services.md) · [admin dashboard](admin-dashboard.md) · [real-time](realtime.md) · [database](database.md) · [auth](authentication.md).

## 3. Recipes

### A. Add a public page
1. **Route**, with no middleware, in `routes/web.php`.
2. **View** `resources/views/<name>.blade.php`:
   ```blade
   <x-guest-layout title="About">
       …
   </x-guest-layout>
   ```
   Options: `:flush="true"` (no vertical padding), `:hide-footer="true"`, `:hide-header="true"` (full-screen displays).
3. **Link it** in `resources/views/partials/header.blade.php` (add `['label' => 'About', 'route' => 'about']` to `$links`) and, if wanted, `partials/footer.blade.php`.

Public pages are always light, whatever the OS theme (see `darkMode` in `tailwind.config.js`).

### B. Add a read-only admin page (a table)
1. **Route** inside the `admin` group in `routes/web.php` (already `auth` + `verified` + `user_type:admin`).
2. **Controller method** returning a view; keep queries in a model scope or service.
3. **View** `resources/views/admin/<name>.blade.php`:
   ```blade
   <x-app-layout title="Reports" :search="true">   {{-- :search shows the topbar search box --}}
       <x-page-header title="Reports" subtitle="Last 30 days" />
       <x-panel class="overflow-hidden"> … </x-panel>
   </x-app-layout>
   ```
   Search reads `?q=`, sorting reads `?sort=&dir=`. Reuse `ClientQueues::scopeSearch` / `scopeSortBy` as the pattern, `<x-sortable-th>` for headers, and `paginate(10)->withQueryString()`.
4. **Nav link**: see recipe D.

### C. Add an admin create / edit / delete (CRUD) page
Copy **Services**; it's the smallest complete example.

| Step | File(s) |
|---|---|
| Table + model | new migration, `app/Models/Thing.php` (`$fillable`, `$casts`) |
| Validation | `app/Http/Requests/Admin/ThingRequest.php` (abstract: `authorize()` returns `user_type === 'admin'`, shared `rules()`), then `StoreThingRequest` / `UpdateThingRequest` extending it |
| Controller | `app/Http/Controllers/AdminThingController.php`: `index()` returns the view; `store/update/destroy` return `jsonSuccess()` / `jsonError()` (from `Controller.php`) |
| Routes | in the `admin` group: |
| Views | `admin/things.blade.php` (page), `partials/admin/thing-table.blade.php`, `partials/admin/thing-modal.blade.php` |
| JS | none; any form with `data-ajax-form` is handled by `admin-forms.js` |
| Nav / docs | recipe D; add a `docs/admin-things.md` and a row in section 2 |

```php
Route::controller(AdminThingController::class)->group(function () {
    Route::get('/things', 'index')->name('admin.things');
    Route::post('/things', 'store')->name('things.store');
    Route::put('/things/{id}', 'update')->whereNumber('id')->name('things.update');
    Route::delete('/things/{id}', 'destroy')->whereNumber('id')->name('things.destroy');
});
```

In the views, the modal is `<x-ajax-form-modal :name="…" :title="…" :action="…" :method="…">` with your fields as its slot. Open it with `x-on:click.prevent="$dispatch('open-modal', 'create-thing')"`. Deleting uses `<x-confirm-delete-modal>`. Put modals **inside a `<td>`**, never as a direct child of `<tr>`; browsers move invalid table content and break the form.

### D. Add a link to the sidebar
Add a row to the `$nav` collection at the top of `resources/views/layouts/sidebar-nav.blade.php`:
```php
['label' => __('Reports'), 'icon' => 'clipboard', 'href' => route('admin.reports'), 'match' => 'admin.reports*', 'section' => __('Manage'), 'show' => $isAdmin],
```
`section` groups rows under a small heading (`null` = no heading), `show` hides the row for other roles, `icon` is a file in `resources/views/components/icon/`; `match` is the route-name pattern that highlights the row.

### E. Add a staff counter action (e.g. "Hold")
1. **Service**: add a method to `app/Services/QueueCallService.php`. Use `DB::transaction` + `lockForUpdate()`, return `['success' => true, …]` or `['success' => false, 'message' => '…', 'status' => 422]`. Fire the event **after** the transaction:
   ```php
   $this->broadcastSafely(fn () => event(new QueueNextEvent($ticket)));
   ```
2. **Controller**: a method in `StaffController` that returns `$this->respond($this->queueCalls->hold($this->currentServiceId()))`.
3. **Route**: in the `staff` group of `routes/web.php` (`auth`, `verified`, `user_type:staff`).
4. **Button**: in `resources/views/staff/dashboard.blade.php`, with an `id`.
5. **JS**: add the id to the `ACTIONS` map in `resources/js/counter.js` (`url` + the toast text).
6. **Test**: a Feature test that signs in as staff and asserts the state change and a 403 for an admin.

### F. Add a real-time event
1. **Event**: `app/Events/QueueHoldEvent.php`, extending `QueueTicketEvent`, with `broadcastAs(): string` returning `'queue.hold'`. The base payload is ids and numbers only. **Never put names or other personal data in a broadcast**; the `queue` channel is public. (`QueueCallEvent` is the one deliberate exception, because the announcement is the point.)
2. **Dispatch** from a service through `broadcastSafely(...)` so a down websocket can never fail the real action.
3. **Listen** in the page script:
   ```js
   import { listen, onReconnect, poll } from './lib/realtime';
   listen(['.queue.hold'], reload);   // re-fetch real state; don't trust the payload
   onReconnect(reload);               // events sent while offline are gone
   poll(reload);                      // fallback while the socket is down
   ```
4. Add a row to the events table in [realtime.md](realtime.md).

### G. Add a table or column
1. `php artisan make:migration add_thing_to_client_queues_table`. **Always a new file.**
2. The app runs on **MySQL, PostgreSQL (production) and SQLite (tests)**. Keep migrations portable: avoid raw driver-specific SQL; if you must, branch on `DB::getDriverName()`. Widening an enum is the known tricky case; copy `2026_09_05_000000_add_priority_and_skipped_status_to_client_queues_table.php`.
3. Add the column to the model's `$fillable` / `$casts`; index anything you filter or sort by.
4. Update the factory/seeder if needed, and [database.md](database.md).
5. `composer test` runs your migration on SQLite, which catches most portability slips.

### H. Add a new ticket status
Today: `waiting`, `serving`, `finish`, `skipped`.
1. Migration widening the `status` column (see recipe G).
2. `app/Models/ClientQueues.php`: any scopes, and the `CASE` in `scopeWorkOrder()`.
3. `app/Services/QueueCallService.php`: the transitions in and out.
4. `app/Services/QueueStatsService.php`: counts, if the new status should appear in them.
5. `resources/views/components/status-badge.blade.php`: label and colour.
6. Tests.

### I. Add a department
No code. An admin adds it at **Admin → Services**. Its slug is generated automatically, and it then appears on the kiosk, the homepage, the all-services board, and gets its own board at `/display/{slug}`. Assign staff in **Admin → Staff**. For seeded defaults, edit `database/seeders/ServiceSeeder.php`.

### J. Add a role (`user_type`)
1. Migration for the `users.user_type` column; `User` model if it needs helpers.
2. `routes/web.php`: a new group with `->middleware(['auth', 'verified', 'user_type:newrole'])`. The middleware also accepts a list: `user_type:admin,staff`.
3. Allow it in the `/dashboard/{user_type}` route's `whereIn(...)`, then handle it in `UserController::index()` and add a dashboard view.
4. Sidebar: set `'show'` for it in `layouts/sidebar-nav.blade.php`.

### K. Add a UI component or icon
- **Component**: `resources/views/components/thing.blade.php`, used as `<x-thing>`. Use `@props([...])` and `$attributes->merge([...])`. If several views repeat the same markup, make it a component.
- **Icon**: `resources/views/components/icon/thing.blade.php`; copy any existing icon file for the wrapper, then use `<x-icon.thing class="h-5 w-5" />`.
- Tailwind scans `resources/views` and `resources/js`, so new classes work after `npm run dev` / `npm run build`.

### L. Add page behavior (JavaScript)
1. Create `resources/js/thing.js`. Wrap it in `onReady(...)` and **only act if the page has your element** (e.g. `if (!byId('thing-root')) return;`), since `app.js` loads every script on every page.
2. `import './thing';` in `resources/js/app.js`.
3. Use the helpers in `resources/js/lib/`: `request` / `post` (CSRF + error handling), `toast`, `escapeHtml`, `listen`, `poll`.
4. Anything built into `innerHTML` that came from a user (a visitor's name, for example) **must** go through `escapeHtml()`.

### M. Add a test
Pest tests live in `tests/Feature/<Area>/<Name>Test.php`. `RefreshDatabase` is applied automatically, and the suite uses in-memory SQLite (`phpunit.xml`).
```php
test('staff cannot open the admin queues page', function () {
    $staff = User::factory()->create(['user_type' => 'staff']);

    $this->actingAs($staff)->get('/admin/queues')->assertForbidden();
});
```
Run: `composer test`, or `php artisan test --filter="staff cannot"`.

### N. Add configuration
Add the variable to `.env.example` with a one-line comment, read it through a `config/*.php` file, and use `config('…')` in code. Don't call `env()` outside `config/`: it returns `null` once `config:cache` runs in production.

## 4. Before you commit
- [ ] `composer test` passes and `npm run build` succeeds.
- [ ] New routes sit in the correct middleware group (public / `auth` / `user_type:…`).
- [ ] Anything a user types is validated in a FormRequest and escaped on output.
- [ ] Broadcasts carry no personal data and go through `broadcastSafely`.
- [ ] The feature map in section 2 and the relevant `docs/*.md` page are updated.

## 5. Folder map

```
app/
  Concerns/BroadcastsSafely.php      Wraps event dispatch so a down websocket never fails an action
  Events/                            QueueTicketEvent (base) + Call / Next / Updated
  Http/Controllers/                  Thin controllers (+ Auth/ from Breeze)
  Http/Middleware/                   EnsureUserType, ProtectDemoAccounts, UpdateLastSeen
  Http/Requests/                     Validation (+ Admin/, Auth/)
  Models/                            ClientQueues, Service, User
  Services/                          Business logic
  View/Components/                   AppLayout, GuestLayout (the <x-app-layout> / <x-guest-layout> props)
config/                              demo.php (demo logins), reverb.php, broadcasting.php, …
database/{migrations,seeders,factories}
resources/
  css/app.css                        Tailwind entry + a few shared component classes
  js/                                app.js (entry) · counter.js · display.js · admin-forms.js · print.js ·
                                     live-status.js · echo.js · lib/{http,dom,realtime,toast}.js
  views/                             admin/ staff/ kiosk/ display/ auth/ profile/ layouts/ partials/ components/
routes/                              web.php (everything) · auth.php (login & passwords) · channels.php
tests/Feature/                       Pest tests
docs/                                You are here
```
