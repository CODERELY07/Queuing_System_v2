<?php

namespace App\Services;

use App\Concerns\BroadcastsSafely;
use App\Events\QueueUpdatedEvent;
use App\Models\ClientQueues;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

/**
 * Kiosk ticket issuance — turning a validated {name, service_id, priority}
 * into a numbered ticket. Pulled out of ClientQueueController so the
 * numbering/locking logic can be reused or tested without an HTTP request.
 */
class ClientQueueService
{
    use BroadcastsSafely;

    public function createTicket(array $data): ClientQueues
    {
        $queue = null;

        DB::transaction(function () use ($data, &$queue) {
            // withTrashed(): (service_id, queue_number) is uniquely
            // constrained at the DB level regardless of soft-deletes, so a
            // number "freed up" by archiving/deleting a ticket is still
            // occupied by that trashed row. Without this, the very next
            // registration for that service after an admin archives one
            // would recompute the same number and crash on the unique
            // constraint — tickets are never renumbered, only ever issued.
            //
            // orderByDesc()->value(), not max(): MySQL allows `SELECT
            // MAX(...) ... FOR UPDATE`, locking every row it scans to
            // compute the aggregate, but Postgres rejects FOR UPDATE
            // combined with an aggregate function outright ("Feature not
            // supported"). Selecting and locking the single highest row
            // directly works the same way on both — the next concurrent
            // request for this service blocks on that same row until this
            // transaction commits, then sees the number it just issued.
            $last = ClientQueues::withTrashed()
                ->where('service_id', $data['service_id'])
                ->lockForUpdate()
                ->orderByDesc('queue_number')
                ->value('queue_number');

            $queue = ClientQueues::create([
                'name' => $data['name'],
                'service_id' => $data['service_id'],
                'queue_number' => $last ? $last + 1 : 1,
                'priority' => $data['priority'] ?? false,
            ]);
        });

        $queue->setRelation('service', Service::find($data['service_id']));

        // Lets that department's staff dashboard pick up the new ticket
        // live — see queuing.js's `.queue.updated` listener. Wrapped: a
        // visitor getting a ticket must never fail just because the
        // real-time layer isn't reachable (see BroadcastsSafely).
        $this->broadcastSafely(fn () => event(new QueueUpdatedEvent($queue)));

        return $queue;
    }
}
