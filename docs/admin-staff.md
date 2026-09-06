# Admin — Staff Management

Full CRUD on staff logins.

- **Routes**: `GET /admin/staff`, `POST /admin/store`, `PUT /admin/staff/update/{id}`, `DELETE /admin/staff/destroy/{id}`
- **Controller**: `App\Http\Controllers\AdminStaffController`
- **Requests**: `App\Http\Requests\Admin\StoreStaffRequest`, `App\Http\Requests\Admin\UpdateStaffRequest`
- **Views**: `resources/views/admin/staff.blade.php`, `resources/views/partials/admin/staff-table.blade.php`, `resources/views/partials/admin/staff-modal.blade.php`
- **Frontend**: `resources/js/staff.js` (generic AJAX handler shared with [Service Management](admin-services.md))
- **Access**: `user_type:admin` only

## Features

- **List** — every account with `user_type = staff`, with its department, searchable by name/email and sortable by name/email (defaults to newest-first).
- **Add** — name, email, department, password (+ confirmation). Creating always sets `user_type = 'staff'` server-side — there is no way to create an admin account through this form.
- **Edit** — same fields, but the password is optional: leave both password fields blank to keep the current one.
- **Delete** — with a confirmation modal.

All three write actions go through AJAX (`fetch`, not a form POST navigation) and return `{status: 'success'|'error', message}`, rendered as a JS `alert()` — a page reload only happens on success.

## Validation

| Field | Create (`StoreStaffRequest`) | Update (`UpdateStaffRequest`) |
|---|---|---|
| `name` | required, string, max 50, unique across **all** users | same, but ignores this record's own current value |
| `email` | required, valid email, unique across all users | same, ignoring this record |
| `service_id` | required, integer, must exist in `services` | same |
| `password` | required, confirmed | **optional** (`nullable`), confirmed if present |

A validation failure returns a proper `422 {errors: {field: [...]}}` response, which the frontend (`staff.js`) already parses into a single alert listing every problem — see [architecture.md](architecture.md#why-split-it-this-way) for why this only works correctly once validation moved into a Form Request instead of living inside a `try/catch`.

## Why every write is scoped to `user_type = 'staff'`

```php
// update()
User::where('user_type', 'staff')->where('id', $id)->update($validated);

// destroy()
User::where('user_type', 'staff')->where('id', $id)->delete();
```

The `{id}` in these routes is a bare numeric id with no other scoping. Without the `user_type = 'staff'` condition, a crafted request to `PUT /admin/staff/update/1` (or whatever an admin's own id happens to be) could silently overwrite an **admin** account's password, or delete it — including the caller's own account — since nothing else would stop it from matching. This was a real, exploitable gap; it's closed by scoping both queries to staff-only rows, so they simply match zero rows (`"No changes were made." / "Delete failed."`) if pointed at anything else.

## What's deliberately *not* here

The department dropdown (`Service::with('users')->get()`) lists every department, including the internal "Admin" one — there's no guard stopping an admin from assigning a staff account to it. In practice nobody does this (it wouldn't do anything useful: the staff dashboard would just show an empty, un-callable queue for a department that doesn't accept kiosk tickets — see [kiosk.md](kiosk.md)), so this hasn't needed fixing, but it's a gap worth knowing about if this page is extended.
