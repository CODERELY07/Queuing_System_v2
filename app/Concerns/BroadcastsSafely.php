<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Wraps a broadcast event dispatch so a broken or unreachable broadcaster
 * — Reverb not running, missing REVERB_* env vars, a network hiccup —
 * can never turn a real action (a ticket issued, a patient called) into a
 * 500 error for the person doing it. The database write has already
 * succeeded by the time any of these fire; the live update is a
 * nice-to-have layered on top of that, never a precondition for it.
 *
 * This is exactly the gap that made the kiosk work locally (where
 * `php artisan reverb:start` is running) and 500 in production (a single
 * web process with nothing serving Reverb) — the ticket was being created
 * fine; broadcasting it live was what threw.
 */
trait BroadcastsSafely
{
    protected function broadcastSafely(\Closure $dispatch): void
    {
        try {
            $dispatch();
        } catch (Throwable $e) {
            Log::warning('Broadcast failed, continuing without it: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
