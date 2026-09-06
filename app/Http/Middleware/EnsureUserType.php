<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to one or more `user_type` values, e.g.
 * ->middleware('user_type:admin') or 'user_type:admin,staff'.
 *
 * Replaces the ad hoc `abort_unless(...user_type === ...)` check that used
 * to live inside StaffController — and closes a real gap the admin route
 * group had: it required *some* login (`auth`), but nothing checked the
 * logged-in user was actually an admin, so a staff account could reach
 * every /admin/* route just by knowing the URL.
 */
class EnsureUserType
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        abort_unless(
            $request->user() && in_array($request->user()->user_type, $types, true),
            403
        );

        return $next($request);
    }
}
