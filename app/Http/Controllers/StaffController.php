<?php

namespace App\Http\Controllers;

use App\Services\QueueCallService;
use App\Services\StaffQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(
        private readonly QueueCallService $queueCalls,
        private readonly StaffQueueService $tickets,
    ) {
    }

    public function data(): JsonResponse
    {
        return response()->json($this->queueCalls->dashboardSnapshot($this->currentServiceId()));
    }

    /**
     * Just the ticket table's markup, for counter.js to swap in when a new
     * ticket arrives — far cheaper than re-rendering (and re-downloading)
     * the whole dashboard page to pick up one row.
     */
    public function queueTable(Request $request): View
    {
        return view('staff.partials.queue-table', [
            'queues' => $this->tickets->paginate($this->currentServiceId(), $request),
        ]);
    }

    public function callNext(): JsonResponse
    {
        return $this->respond($this->queueCalls->callNext($this->currentServiceId()));
    }

    public function callPrevious(): JsonResponse
    {
        return $this->respond($this->queueCalls->callPrevious($this->currentServiceId()));
    }

    public function skip(): JsonResponse
    {
        return $this->respond($this->queueCalls->skip($this->currentServiceId()));
    }

    public function call(): JsonResponse
    {
        return $this->respond($this->queueCalls->recall($this->currentServiceId()));
    }

    public function selectedCall(int $id): JsonResponse
    {
        return $this->respond($this->queueCalls->callSelected($this->currentServiceId(), $id));
    }

    /**
     * The service the currently authenticated staff member belongs to.
     * The `user_type:staff` route middleware (see routes/web.php) already
     * guarantees the caller is staff, so this is just the lookup.
     */
    private function currentServiceId(): int
    {
        return Auth::user()->service_id;
    }

    /**
     * Turn a service result (`['success' => bool, 'status' => ?int, ...]`)
     * into a JSON response with the right HTTP status.
     */
    private function respond(array $result): JsonResponse
    {
        return response()->json($result, $result['status'] ?? 200);
    }
}
