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


class QueueNextEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $next;
    /**
     * Create a new event instance.
     */
    public function __construct($next)
    {
        $this->next = $next;

        Log::debug("Queue Event", 
        [
            'id' => $this->next->id,
            'name' => $this->next->name,
            'status' => $this->next->status,
            'service_id' => $this->next->service_id,
            'queue_number' => $this->next->queue_number
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn():Channel
    {
        return new Channel('queue');
    }
    
    public function broadcastAs(){
        return 'queue.next';
    }

    public function broadcastWith(){
        return [
            'id' => $this->next->id,
            'name' => $this->next->name,
            'status' => $this->next->status,
            'service_id' => $this->next->service_id,
            'queue_number' => $this->next->queue_number
        ];
    }
}
