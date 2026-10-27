<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops the shared demo accounts from being edited or deleted.
 *
 * Their credentials are printed on the login page (config/demo.php), so
 * anyone can sign in as them — and without this, anyone could then change
 * that account's email or password, or delete it, locking everyone who
 * comes after out of the demo until someone re-seeds the database.
 *
 * Applied to the three profile actions that change or remove an account.
 * It does nothing when DEMO_ACCOUNTS is off, or for any non-demo account.
 */
class ProtectDemoAccounts
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isDemoAccount()) {
            return back()->with('error', "This is a shared demo account, so its details can't be changed or deleted.");
        }

        return $next($request);
    }
}
