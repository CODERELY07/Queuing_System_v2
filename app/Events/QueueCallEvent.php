<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class QueueCallEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

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

    public function broadcastAs(){
        return 'queue.call';
    }
       public function broadcastWith(){
        return [
            'id' => $this->ticket->id,
            'name' => $this->ticket->name,
            'status' => $this->ticket->status,
            'service_id' => $this->ticket->service_id,
            'queue_number' => $this->ticket->queue_number
        ];
    }
}
