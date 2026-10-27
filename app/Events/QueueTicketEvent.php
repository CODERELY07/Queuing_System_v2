<?php

namespace App\Events;

use App\Models\ClientQueues;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Shared shape of every real-time queue event: one ticket, broadcast on the
 * public `queue` channel. The four concrete events differ only in their
 * client-facing name (see broadcastAs()) — they used to be four
 * near-identical classes.
 *
 * The channel is public, and its app key ships in the page's JavaScript, so
 * anyone can subscribe. The payload is therefore the bare minimum a
 * listener needs to decide whether to refresh: ids and numbers, never
 * personal data. Listeners re-fetch the real state over HTTP rather than
 * trusting the payload. The single exception is QueueCallEvent, which
 * carries the name because announcing it to the waiting room is the whole
 * point of that event.
 */
abstract class QueueTicketEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ClientQueues $ticket)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('queue');
    }

    /**
     * The event name Echo listens for (with a leading dot, since it's not
     * namespaced), e.g. 'queue.call'.
     */
    abstract public function broadcastAs(): string;

    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticket->id,
            'status' => $this->ticket->status,
            'service_id' => $this->ticket->service_id,
            'queue_number' => $this->ticket->queue_number,
        ];
    }
}
