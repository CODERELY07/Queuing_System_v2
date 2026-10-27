<?php

namespace App\Services;

use App\Concerns\BroadcastsSafely;
use App\Events\QueueUpdatedEvent;
use App\Models\ClientQueues;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

/**
 * Kiosk ticket issuance — turning a validated {name, service_id, priority}
 * into a numbered ticket, and estimating how long it'll take to be called.
 * Pulled out of ClientQueueController so the numbering/locking logic can be
 * reused or tested without an HTTP request.
 */
class ClientQueueService
{
    use BroadcastsSafely;

    /**
     * Rough minutes of counter time per person ahead of you. A flat guess:
     * there's no per-visit duration recorded to average over (see
     * QueueStatsService::avgTurnaroundMinutesToday() for the closest proxy).
     */
    private const MINUTES_PER_TICKET = 2;

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
        // live — see counter.js's `.queue.updated` listener. Wrapped: a
        // visitor getting a ticket must never fail just because the
        // real-time layer isn't reachable (see BroadcastsSafely).
        $this->broadcastSafely(fn () => event(new QueueUpdatedEvent($queue)));

        return $queue;
    }

    /**
     * Estimated minutes until this ticket is called: the people ahead of it
     * today × a flat per-person guess.
     *
     * "Ahead" follows the counter's own call order (see
     * ClientQueues::scopeNextInLine): whoever's being served right now,
     * plus the waiting tickets that sort before this one — every priority
     * ticket for a regular ticket, only earlier priority tickets for a
     * priority one. This replaces `queue_number × 2`, which ignored both
     * the priority lane and the fact that ticket numbers never reset — so
     * ticket 150 was told to expect a 300-minute wait.
     */
    public function estimatedWaitMinutes(ClientQueues $queue): int
    {
        $ahead = ClientQueues::forService($queue->service_id)
            ->today()
            ->whereKeyNot($queue->getKey())
            ->where(function ($query) use ($queue) {
                $query->where('status', 'serving')
                    ->orWhere(function ($waiting) use ($queue) {
                        $waiting->where('status', 'waiting')
                            ->where(function ($sorted) use ($queue) {
                                // Earlier in the same lane...
                                $sorted->where(fn ($lane) => $lane
                                    ->where('priority', $queue->priority)
                                    ->where('queue_number', '<', $queue->queue_number));

                                // ...and, for a regular ticket, the whole priority lane.
                                if (!$queue->priority) {
                                    $sorted->orWhere('priority', true);
                                }
                            });
                    });
            })
            ->count();

        return $ahead * self::MINUTES_PER_TICKET;
    }
}
