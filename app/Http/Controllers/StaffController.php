<?php

namespace App\Http\Controllers;

use App\Events\QueueCallEvent;
use App\Events\QueueNextEvent;
use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function data()
    {
        $service = $this->currentServiceId();

        $recent = ClientQueues::status('waiting')->forService($service)->nextInLine()->take(3)->get()->map(function ($q) {
            return [
                'number' => $q->formattedNumber(),
                'status' => ucfirst($q->status),
                'priority' => $q->priority,
            ];
        });

        // Scoped to today so this matches the "Waiting today" stat card the
        // staff dashboard renders server-side and then keeps polling here.
        $waitingCount = ClientQueues::status('waiting')->forService($service)->whereDate('created_at', now())->count();

        $servingPatient = ClientQueues::status('serving')
            ->forService($service)
            ->orderBy('queue_number')
            ->first();

        if ($servingPatient) {
            $formatted = [
                'number' => $servingPatient->formattedNumber(),
                'name' => ucfirst($servingPatient->name),
                'priority' => $servingPatient->priority,
            ];
        } elseif ($waitingCount > 0) {
            $formatted = [
                'number' => "Start Queue",
                'name' => "Patient is waiting...",
                'priority' => false,
            ];
        } else {
            $formatted = [
                'number' => "No Queue",
                'name' => "No work",
                'priority' => false,
            ];
        }

        return response()->json([
            'waiting' => $waitingCount,
            'recent' => $recent,
            'servingPatient' => $formatted
        ]);
    }

    public function callNext()
    {
        $service = $this->currentServiceId();

        $waiting = ClientQueues::status('waiting')
            ->forService($service)
            ->nextInLine()
            ->first();

        $next = ClientQueues::status('serving')
            ->forService($service)
            ->orderBy('queue_number')
            ->first();

        if (!empty($waiting) || !empty($next)) {
            if (!empty($waiting)) {
                event(new QueueNextEvent($waiting));
                $waiting->update(['status' => 'serving']);
            }

            if (!empty($next)) {
                event(new QueueNextEvent($next));
                $next->update(['status' => 'finish']);
            }
            $active = $waiting ?? $next;
            $number = $active->formattedNumber();

        } else {
            $number = 'None';
        }


        return response()->json(['number' => $number]);
    }

    public function callPrevious()
    {
        $serviceId = $this->currentServiceId();

        $current = ClientQueues::status('serving')->forService($serviceId)->first();

        $previous = ClientQueues::forService($serviceId)
            ->status('finish')
            ->orderBy('queue_number', 'desc')
            ->first();

        if ($previous) {
            event(new QueueNextEvent($previous));
            $current?->update(['status' => 'waiting']);
            $previous?->update(['status' => 'serving']);

            $number = $previous->formattedNumber();
        } else {
            $number = 'None';
        }

        return response()->json(['number' => $number]);
    }

    /**
     * Mark the currently-serving ticket a no-show: it was called and the
     * counter is done waiting on it. Doesn't touch the display or the
     * voice announcement — a skip is a counter-side bookkeeping action,
     * not a new call.
     */
    public function skip()
    {
        $current = ClientQueues::status('serving')->forService($this->currentServiceId())->first();

        if (!$current) {
            return response()->json(['success' => false, 'message' => 'No ticket is being served.'], 422);
        }

        $current->update(['status' => 'skipped']);

        return response()->json(['success' => true, 'number' => $current->formattedNumber()]);
    }

    public function call(){
        $current = ClientQueues::status('serving')->forService($this->currentServiceId())->first();

        // Nothing being served yet (e.g. first action of the shift) — there's
        // no ticket to re-announce. Without this guard the null model blew
        // up inside the event's broadcastWith() and the client saw an HTML
        // error page where it expected JSON.
        if (!$current) {
            return response()->json(['success' => false, 'message' => 'No ticket is being served.'], 422);
        }

        event(new QueueCallEvent($current));

        return response()->json(['success' => true]);
    }

    public function selectedCall($id){
        $serviceId = $this->currentServiceId();

        $next = ClientQueues::where('id', $id)->forService($serviceId)->first();

        if (!$next) {
            return response()->json(['success' => false, 'message' => 'Ticket not found.'], 404);
        }

        // Same fix as above: there may be no one currently serving to bump
        // back to waiting, and that's fine — it just means the counter was
        // idle before this call.
        $current = ClientQueues::status('serving')->forService($serviceId)->first();
        $current?->update(['status' => 'waiting']);

        $next->update(['status' => 'serving']);
        event(new QueueCallEvent($next));

        return response()->json(['success' => true]);
    }

    /**
     * The service the currently authenticated staff member belongs to.
     */
    private function currentServiceId()
    {
        return Auth::user()->service_id;
    }

}
