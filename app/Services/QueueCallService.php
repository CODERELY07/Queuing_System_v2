<?php

namespace App\Services;

use App\Events\QueueCallEvent;
use App\Events\QueueNextEvent;
use App\Models\ClientQueues;

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
 */
class QueueCallService
{
    /**
     * Everything the staff dashboard's top panel needs: the waiting count,
     * up to 3 tickets up next, and whoever's currently being served (or a
     * synthetic "start queue" / "no queue" placeholder when nobody is).
     */
    public function dashboardSnapshot(int $serviceId): array
    {
        $recent = ClientQueues::status('waiting')
            ->forService($serviceId)
            ->nextInLine()
            ->take(3)
            ->get()
            ->map(fn ($q) => [
                'number' => $q->formattedNumber(),
                'status' => ucfirst($q->status),
                'priority' => $q->priority,
            ]);

        // Scoped to today so this matches the "Waiting today" stat card the
        // staff dashboard renders server-side and then keeps polling here.
        $waitingCount = ClientQueues::status('waiting')
            ->forService($serviceId)
            ->whereDate('created_at', now())
            ->count();

        $servingPatient = ClientQueues::status('serving')
            ->forService($serviceId)
            ->orderBy('queue_number')
            ->first();

        if ($servingPatient) {
            $formatted = [
                'number' => $servingPatient->formattedNumber(),
                'name' => ucfirst($servingPatient->name),
                'priority' => $servingPatient->priority,
            ];
        } elseif ($waitingCount > 0) {
            $formatted = ['number' => 'Start Queue', 'name' => 'Patient is waiting...', 'priority' => false];
        } else {
            $formatted = ['number' => 'No Queue', 'name' => 'No work', 'priority' => false];
        }

        return [
            'waiting' => $waitingCount,
            'recent' => $recent,
            'servingPatient' => $formatted,
        ];
    }

    /**
     * Finish whoever's being served (if anyone) and call the next ticket
     * in line (priority first, then oldest).
     */
    public function callNext(int $serviceId): array
    {
        $waiting = ClientQueues::status('waiting')->forService($serviceId)->nextInLine()->first();
        $current = ClientQueues::status('serving')->forService($serviceId)->orderBy('queue_number')->first();

        $current?->update(['status' => 'finish']);

        if ($waiting) {
            $waiting->update(['status' => 'serving']);
            $number = $waiting->formattedNumber();
        } else {
            // Nothing waiting — the counter goes idle.
            $number = 'None';
        }

        // One refresh signal for the waiting-room display, fired after
        // both status changes have landed. The listener re-fetches live
        // state rather than trusting the event payload (see queuing.js),
        // so one firing covers whichever ticket actually changed.
        if ($waiting || $current) {
            event(new QueueNextEvent($waiting ?? $current));
        }

        return ['success' => true, 'number' => $number];
    }

    /**
     * Undo the last call: bring back whichever ticket most recently
     * finished or was skipped, and put whoever's currently serving back to
     * waiting.
     */
    public function callPrevious(int $serviceId): array
    {
        $current = ClientQueues::status('serving')->forService($serviceId)->first();

        // Includes 'skipped', not just 'finish' — if the last thing that
        // happened was a no-show, "Previous Patient" should undo *that*,
        // not reach past it for whichever ticket finished before it.
        $previous = ClientQueues::forService($serviceId)
            ->whereIn('status', ['finish', 'skipped'])
            ->orderBy('queue_number', 'desc')
            ->first();

        if (!$previous) {
            return ['success' => true, 'number' => 'None'];
        }

        event(new QueueNextEvent($previous));
        $current?->update(['status' => 'waiting']);
        $previous->update(['status' => 'serving']);

        return ['success' => true, 'number' => $previous->formattedNumber()];
    }

    /**
     * Mark the currently-serving ticket a no-show.
     */
    public function skip(int $serviceId): array
    {
        $current = ClientQueues::status('serving')->forService($serviceId)->first();

        if (!$current) {
            return ['success' => false, 'message' => 'No ticket is being served.', 'status' => 422];
        }

        $current->update(['status' => 'skipped']);

        // The counter is idle again now — without this, the waiting-room
        // display kept showing the no-show ticket as "currently being
        // served" until the next real call finally overwrote it.
        event(new QueueNextEvent($current));

        return ['success' => true, 'number' => $current->formattedNumber()];
    }

    /**
     * Re-announce whoever's currently being served.
     */
    public function recall(int $serviceId): array
    {
        $current = ClientQueues::status('serving')->forService($serviceId)->first();

        if (!$current) {
            return ['success' => false, 'message' => 'No ticket is being served.', 'status' => 422];
        }

        event(new QueueCallEvent($current));

        return ['success' => true];
    }

    /**
     * Call a specific ticket out of order (e.g. picking one off the table
     * on the dashboard rather than following the priority/FIFO line).
     */
    public function callSelected(int $serviceId, int $ticketId): array
    {
        $next = ClientQueues::where('id', $ticketId)->forService($serviceId)->first();

        if (!$next) {
            return ['success' => false, 'message' => 'Ticket not found.', 'status' => 404];
        }

        // A finished ticket has nothing left to call — without this, the
        // "Call" button could resurrect a completed visit as "currently
        // serving" again.
        if ($next->status === 'finish') {
            return ['success' => false, 'message' => 'This ticket is already finished.', 'status' => 422];
        }

        $current = ClientQueues::status('serving')->forService($serviceId)->first();
        $current?->update(['status' => 'waiting']);

        $next->update(['status' => 'serving']);
        event(new QueueCallEvent($next));

        return ['success' => true];
    }
}
