<?php

namespace App\Events;

/**
 * A ticket is being (re)called — the waiting-room display flashes the
 * number and speaks the announcement aloud.
 *
 * The only event whose payload includes the visitor's name: it's read out
 * to the room and shown on the board either way.
 */
class QueueCallEvent extends QueueTicketEvent
{
    public function broadcastAs(): string
    {
        return 'queue.call';
    }

    public function broadcastWith(): array
    {
        return parent::broadcastWith() + ['name' => $this->ticket->name];
    }
}
