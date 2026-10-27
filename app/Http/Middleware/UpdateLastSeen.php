<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps `users.last_seen` current — it's what the admin dashboard's
 * "Staff Active" tile reads (anyone seen in the last 5 minutes).
 */
class UpdateLastSeen
{
    /**
     * How stale `last_seen` may get before it's rewritten. It used to be
     * rewritten on *every* authenticated request — one extra database write
     * per page view, per poll, per live refresh. Against a 5-minute
     * "active" window, a minute of granularity changes nothing anyone can
     * see.
     */
    private const REFRESH_AFTER_SECONDS = 60;

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (!$user->last_seen || $user->last_seen->lt(now()->subSeconds(self::REFRESH_AFTER_SECONDS)))) {
            // Quietly: a presence ping isn't a model change worth firing
            // events for.
            $user->forceFill(['last_seen' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
