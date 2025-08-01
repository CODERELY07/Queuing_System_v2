<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientQueueController extends Controller
{
    public function index()
    {
        $services = Service::all(); 
        return view('kiosk.index', compact('services'));
    }
    public function store(Request $request)
        {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:client_queues,name',
            'service_id' => 'required|exists:services,id',
        ]);

        $queue = null;

        DB::transaction(function () use ($validated, &$queue) {
            $last = ClientQueues::where('service_id', $validated['service_id'])
                ->lockForUpdate()
                ->max('queue_number');

            $next = $last ? $last + 1 : 1;

            $queue = ClientQueues::create([
                'name' => $validated['name'],
                'service_id' => $validated['service_id'],
                'queue_number' => $next,
            ]);
        });

        $prefix = Service::find($validated['service_id'])->prefix ?? 'Q';

        $queue->prefix = $prefix;

        $estimatedWaitTime = $queue->queue_number * 2; 

        return view('kiosk.ticket', [
            'queue' => $queue,
            'estimatedWaitTime' => $estimatedWaitTime
        ]);
    }

    

}
