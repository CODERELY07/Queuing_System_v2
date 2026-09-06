<?php

namespace App\Services;

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
            $last = ClientQueues::withTrashed()
                ->where('service_id', $data['service_id'])
                ->lockForUpdate()
                ->max('queue_number');

            $queue = ClientQueues::create([
                'name' => $data['name'],
                'service_id' => $data['service_id'],
                'queue_number' => $last ? $last + 1 : 1,
                'priority' => $data['priority'] ?? false,
            ]);
        });

        $queue->setRelation('service', Service::find($data['service_id']));

        return $queue;
    }
}
