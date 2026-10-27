<?php

namespace App\Services;

use App\Concerns\BroadcastsSafely;
use App\Events\QueueCallEvent;
use App\Events\QueueNextEvent;
use App\Models\ClientQueues;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * The staff counter's queue-control actions: call next, recall the
 * previous ticket, skip a no-show, re-announce whoever's serving, and call
 * a specific ticket out of order — plus the dashboard's live snapshot.
 *
 * Pulled out of StaffController: none of this is a "resource" action (no
 * route here is a CRUD verb on a queue-call resource), it's the counter's
 * actual business logic, and keeping it here means it can be exercised
 * without going through HTTP.
 *
 * Every mutating method returns a plain `['success' => bool, ...]` array
 * rather than throwing — a staff member hitting "No-show" with nobody
 * being served is an expected, everyday outcome here, not an exceptional
 * one, so the controller just turns this straight into JSON.
 *
 * Anything that changes more than one row runs in a transaction, with the
 * rows locked while it decides: two counters in the same department
 * pressing "Next" at the same moment can no longer both pick the same
 * waiting ticket, and a failure between "finish the current one" and
 * "start the next one" can no longer leave the queue half-moved. Events
 * are dispatched only after the transaction commits, so a listener's
 * re-fetch never sees the old state.
 */
class QueueCallService
{
    use BroadcastsSafely;

    public function __construct(private readonly QueueStatsService $stats)
    {
    }

    /**
     * Everything the staff dashboard's live panels need: today's counters,
     * up to 3 tickets up next, and whoever's currently being served (or a
     * synthetic "start queue" / "no queue" placeholder when nobody is).
     */
    public function dashboardSnapshot(int $serviceId): array
    {
        $counts = $this->stats->todayCounts($serviceId);

        $upNext = ClientQueues::status('waiting')
            ->forService($serviceId)
            ->today()
            ->nextInLine()
            ->take(3)
            ->get()
            ->map(fn ($ticket) => [
                'number' => $ticket->formattedNumber(),
                'priority' => $ticket->priority,
            ]);

        $serving = $this->serving($serviceId)->first();

        if ($serving) {
            $current = [
                'number' => $serving->formattedNumber(),
                'name' => ucfirst($serving->name),
                'priority' => $serving->priority,
            ];
        } elseif ($counts['waiting'] > 0) {
            $current = ['number' => 'Start Queue', 'name' => 'Patient is waiting...', 'priority' => false];
        } else {
            $current = ['number' => 'No Queue', 'name' => 'No work', 'priority' => false];
        }

        return [
            'waiting' => $counts['waiting'],
            'skipped' => $counts['skipped'],
            'finished' => $counts['finished'],
            'recent' => $upNext,
            'servingPatient' => $current,
        ];
    }

    /**
     * Finish whoever's being served (if anyone) and call the next ticket
     * in line (priority first, then oldest).
     */
    public function callNext(int $serviceId): array
    {
        [$finished, $called] = DB::transaction(function () use ($serviceId) {
            // Every serving ticket for the department, not just today's
            // single one — also tidies up a ticket nobody closed out
            // yesterday, which would otherwise stay "serving" forever.
            $finished = ClientQueues::status('serving')->forService($serviceId)->lockForUpdate()->get();

            $called = ClientQueues::status('waiting')
                ->forService($serviceId)
                ->today()
                ->nextInLine()
                ->lockForUpdate()
                ->first();

            $finished->each->update(['status' => 'finish']);
            $called?->update(['status' => 'serving']);

            return [$finished, $called];
        });

        // One refresh signal for the waiting-room display, after both
        // status changes have landed. Listeners re-fetch live state rather
        // than trusting the payload, so one firing covers whichever ticket
        // actually changed.
        $changed = $called ?? $finished->last();

        if ($changed) {
            $this->broadcastSafely(fn () => event(new QueueNextEvent($changed)));
        }

        // Nothing waiting — the counter goes idle.
        return ['success' => true, 'number' => $called?->formattedNumber() ?? 'None'];
    }

    /**
     * Undo the last call: bring back whichever ticket most recently
     * finished or was skipped, and put whoever's currently serving back to
     * waiting.
     */
    public function callPrevious(int $serviceId): array
    {
        $previous = DB::transaction(function () use ($serviceId) {
            // Includes 'skipped', not just 'finish' — if the last thing
            // that happened was a no-show, "Previous Patient" should undo
            // *that*, not reach past it for whichever ticket finished
            // before it.
            $previous = ClientQueues::forService($serviceId)
                ->today()
                ->whereIn('status', ['finish', 'skipped'])
                ->orderByDesc('queue_number')
                ->lockForUpdate()
                ->first();

            if (!$previous) {
                return null;
            }

            $this->serving($serviceId)->lockForUpdate()->get()->each->update(['status' => 'waiting']);
            $previous->update(['status' => 'serving']);

            return $previous;
        });

        if (!$previous) {
            return ['success' => true, 'number' => 'None'];
        }

        $this->broadcastSafely(fn () => event(new QueueNextEvent($previous)));

        return ['success' => true, 'number' => $previous->formattedNumber()];
    }

    /**
     * Mark the currently-serving ticket a no-show.
     */
    public function skip(int $serviceId): array
    {
        $current = DB::transaction(function () use ($serviceId) {
            $current = $this->serving($serviceId)->lockForUpdate()->first();
            $current?->update(['status' => 'skipped']);

            return $current;
        });

        if (!$current) {
            return ['success' => false, 'message' => 'No ticket is being served.', 'status' => 422];
        }

        // The counter is idle again now — without this, the waiting-room
        // display kept showing the no-show ticket as "currently being
        // served" until the next real call finally overwrote it.
        $this->broadcastSafely(fn () => event(new QueueNextEvent($current)));

        return ['success' => true, 'number' => $current->formattedNumber()];
    }

    /**
     * Re-announce whoever's currently being served.
     */
    public function recall(int $serviceId): array
    {
        $current = $this->serving($serviceId)->first();

        if (!$current) {
            return ['success' => false, 'message' => 'No ticket is being served.', 'status' => 422];
        }

        $this->broadcastSafely(fn () => event(new QueueCallEvent($current)));

        return ['success' => true];
    }

    /**
     * Call a specific ticket out of order (e.g. picking one off the table
     * on the dashboard rather than following the priority/FIFO line).
     */
    public function callSelected(int $serviceId, int $ticketId): array
    {
        $outcome = DB::transaction(function () use ($serviceId, $ticketId) {
            $ticket = ClientQueues::forService($serviceId)->lockForUpdate()->find($ticketId);

            if (!$ticket) {
                return ['success' => false, 'message' => 'Ticket not found.', 'status' => 404];
            }

            // A finished ticket has nothing left to call — without this,
            // the "Call" button could resurrect a completed visit as
            // "currently serving" again.
            if ($ticket->status === 'finish') {
                return ['success' => false, 'message' => 'This ticket is already finished.', 'status' => 422];
            }

            // Already at the counter: "Call" on that row just announces it
            // again. It used to run the swap below regardless — which put
            // the ticket back to 'waiting' (as the "current" one) and then
            // failed to set it 'serving' again (no change from the
            // in-memory copy's point of view), quietly un-serving the
            // patient in front of the counter.
            if ($ticket->status !== 'serving') {
                $this->serving($serviceId)->lockForUpdate()->get()->each->update(['status' => 'waiting']);
                $ticket->update(['status' => 'serving']);
            }

            return ['success' => true, 'ticket' => $ticket];
        });

        if (!$outcome['success']) {
            return $outcome;
        }

        $this->broadcastSafely(fn () => event(new QueueCallEvent($outcome['ticket'])));

        return ['success' => true];
    }

    /**
     * Today's serving ticket(s) for a department — the counter's current
     * patient.
     */
    private function serving(int $serviceId): Builder
    {
        return ClientQueues::status('serving')->forService($serviceId)->today()->orderBy('queue_number');
    }
}
