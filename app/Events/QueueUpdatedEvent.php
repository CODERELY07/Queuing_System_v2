<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A new ticket was just issued at the kiosk. Broadcasts so the ticket's own
 * department's staff dashboard can refresh its "Up Next" list and queue
 * table live — without this, a new ticket was invisible to staff until
 * they happened to take an action of their own (which triggers its own
 * refresh) or reloaded the page.
 */
class QueueUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('queue');
    }

    public function broadcastAs()
    {
        return 'queue.updated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->ticket->id,
            'name' => $this->ticket->name,
            'status' => $this->ticket->status,
            'service_id' => $this->ticket->service_id,
            'queue_number' => $this->ticket->queue_number,
        ];
    }
}
