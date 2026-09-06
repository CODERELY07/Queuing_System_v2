<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientQueueRequest;
use App\Models\Service;
use App\Services\ClientQueueService;

class ClientQueueController extends Controller
{
    public function __construct(private readonly ClientQueueService $clientQueues)
    {
    }

    public function index()
    {
        // publicFacing(): excludes the internal "Admin" service — a visitor
        // reaching the kiosk directly (rather than via the home page, which
        // already curates its own list the same way) should never be able
        // to pull a ticket for it.
        $services = Service::publicFacing()->get();
        return view('kiosk.index', compact('services'));
    }

    public function store(StoreClientQueueRequest $request)
    {
        $queue = $this->clientQueues->createTicket($request->validated());

        return view('kiosk.ticket', [
            'queue' => $queue,
            'estimatedWaitTime' => $queue->queue_number * 2,
        ]);
    }
}
