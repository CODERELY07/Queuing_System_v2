<?php

use App\Http\Middleware\EnsureUserType;
use App\Http\Middleware\UpdateLastSeen;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Existed but was never registered anywhere — the admin
        // dashboard's "Staff Active" count (last_seen within 5 minutes)
        // has been permanently stuck at 0 with no staff ever recorded as
        // active. A no-op for guests (it checks auth()->check() first),
        // so appending it web-wide is safe.
        $middleware->web(append: [UpdateLastSeen::class]);

        $middleware->alias([
            'user_type' => EnsureUserType::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
