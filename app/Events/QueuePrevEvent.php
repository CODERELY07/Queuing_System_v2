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

class QueuePrevEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $prev;
    /**
     * Create a new event instance.
     */
    public function __construct($prev)
    {
        $this->prev = $prev;
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
        return 'queue.prev';
    }
    
    public function broadcastWith(){
        return [
            'id' => $this->prev->id,
            'name' => $this->prev->name,
            'status' => $this->prev->status,
            'service_id' => $this->prev->service_id,
            'queue_number' => $this->prev->queue_number
        ];
    }
}
