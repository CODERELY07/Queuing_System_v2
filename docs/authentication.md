# Authentication & Roles

MedQueue's login, registration, and password-reset flows are Laravel Breeze's stock scaffolding, essentially untouched: `app/Http/Controllers/Auth/*`, `resources/views/auth/*`, `routes/auth.php`. If you've used Breeze before, there's nothing custom here beyond what's noted below.

## The two roles

`users.user_type` is an enum with exactly two values: `admin` and `staff`. There is no "visitor" account — the kiosk and public display boards don't require login at all (see [kiosk.md](kiosk.md) and [display-boards.md](display-boards.md)).

## Routing by role

After login, everyone lands on `/dashboard`, which immediately redirects to `/dashboard/{user_type}`:

```php
// routes/web.php
Route::get('/dashboard', function () {
    $user = Auth::user();
    return redirect()->route('dashboard.with_type', ['user_type' => $user->user_type]);
})->middleware(['auth', 'verified']);
```

`UserController::index($user_type)` (the handler for `/dashboard/{user_type}`) first checks the URL's `{user_type}` matches the logged-in user's actual type (`abort(403)` if not — this stops a staff account from loading `/dashboard/admin` directly by URL, independent of the route-group middleware below), then renders `admin.dashboard` or `staff.dashboard` accordingly.

## Restricting `/admin/*` and `/staff/*`

Both route groups carry `auth`, `verified`, and one role check via `EnsureUserType` (`app/Http/Middleware/EnsureUserType.php`, aliased as `user_type` in `bootstrap/app.php`):

```php
Route::prefix('admin')->middleware(['auth', 'verified', 'user_type:admin'])->group(...);
Route::prefix('staff')->middleware(['auth', 'verified', 'user_type:staff'])->group(...);
```

`EnsureUserType` takes one or more allowed types as middleware parameters and `abort(403)`s otherwise:

```php
public function handle(Request $request, Closure $next, string ...$types): Response
{
    abort_unless(
        $request->user() && in_array($request->user()->user_type, $types, true),
        403
    );
    return $next($request);
}
```

This is deliberately generic (`user_type:admin,staff` would work too) so it's the one place role-gating logic lives, rather than being duplicated per controller. Every Admin Form Request (`app/Http/Requests/Admin/*`) also declares `authorize(): bool { return $this->user()?->user_type === 'admin'; }` — redundant with the middleware today, but it's the correct place for a Form Request to state its own authorization rule independent of how a route happens to be wired.

**Why this matters**: before this middleware existed, `/admin/*` only required *some* login — a logged-in staff account could reach every admin page just by knowing the URL, since nothing checked `user_type` specifically. Likewise `/staff/*` had no auth requirement at all until it was added. Both are closed now.

## Email verification

`User` does **not** implement `Illuminate\Contracts\Auth\MustVerifyEmail` (it's explicitly commented out in `app/Models/User.php`). Laravel's `verified` middleware only blocks users who both (a) are logged in and (b) implement that interface — since `User` doesn't, the middleware is present on every protected route group for consistency but never actually blocks anyone. Seeded accounts have no `email_verified_at` set and log in fine.

## Staff activity tracking

`UpdateLastSeen` (`app/Http/Middleware/UpdateLastSeen.php`) is appended to the global `web` middleware group in `bootstrap/app.php`, so it runs on *every* request, authenticated or not:

```php
public function handle($request, Closure $next)
{
    if (auth()->check()) {
        auth()->user()->update(['last_seen' => now()]);
    }
    return $next($request);
}
```

This is what powers the admin dashboard's "Staff Active" count (`last_seen >= now() - 5 minutes`, see [admin-dashboard.md](admin-dashboard.md)). It's a no-op for guests, so appending it web-wide is safe.

## Registration

`/register` exists (Breeze default) but isn't linked from anywhere in the product UI — every account in this app is created by an admin through [Admin — Staff Management](admin-staff.md), or seeded directly. It's left in place rather than removed, but isn't part of the intended user flow.
