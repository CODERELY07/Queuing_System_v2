<?php

namespace App\Events;

/**
 * The counter's "who's being served" changed (next, previous, or a
 * no-show) — displays and other counters in the department re-fetch.
 */
class QueueNextEvent extends QueueTicketEvent
{
    public function broadcastAs(): string
    {
        return 'queue.next';
    }
}
