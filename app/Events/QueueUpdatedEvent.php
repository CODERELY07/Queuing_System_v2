<?php

namespace App\Events;

/**
 * A new ticket was just issued at the kiosk. Broadcasts so the ticket's own
 * department's staff dashboard can refresh its "Up Next" list and queue
 * table live — without this, a new ticket was invisible to staff until
 * they happened to take an action of their own (which triggers its own
 * refresh) or reloaded the page.
 */
class QueueUpdatedEvent extends QueueTicketEvent
{
    public function broadcastAs(): string
    {
        return 'queue.updated';
    }
}
