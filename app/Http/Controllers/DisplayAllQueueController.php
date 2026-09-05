<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use App\Models\Service;
use Illuminate\Http\Request;

class DisplayAllQueueController extends Controller
{
    public function index(){
        // Services listed here too, so the all-services board can link out
        // to each department's own dedicated full-screen display.
        $services = Service::where('name', '!=', 'admin')->get();

        return view('display.counter', compact('services'));
    }

    public function servingPatients()
    {
        $services = Service::where('name', '!=', 'admin')
        ->with(['clientQueues' => function ($query) {
            $query->where('status', 'serving')
                ->orderBy('queue_number')
                ->limit(1);
        }, 'waitingQueues' => function ($query) {
            $query->limit(3);
        }])
        ->get();

            $result = $services->map(function ($service) {
                $serving = $service->clientQueues->first();
                return [
                    'service_name' => $service->name,
                    'service_prefix' => $service->prefix,
                    'serving' => $serving ? [
                        'number' => ClientQueues::formatNumber($serving->queue_number, $service->prefix),
                        'name' => $serving->name,
                    ] : null,
                    // Numbers only, on purpose — the display never shows a
                    // waiting visitor's name, only the person at the counter.
                    'next' => $service->waitingQueues->map(fn ($q) => ClientQueues::formatNumber($q->queue_number, $service->prefix))->values(),
                ];
            });

        return response()->json($result);
    }

    /**
     * The dedicated, full-screen display for a single department.
     */
    public function show(Service $service)
    {
        abort_if(strcasecmp($service->name, 'admin') === 0, 404);

        return view('display.show', compact('service'));
    }

    /**
     * Same shape as one entry of servingPatients(), just scoped to the one
     * service the dedicated display page is polling for.
     */
    public function servingPatient(Service $service)
    {
        $serving = $service->clientQueues()
            ->where('status', 'serving')
            ->orderBy('queue_number')
            ->first();

        $next = $service->waitingQueues()->limit(3)->get();

        return response()->json([
            'service_name' => $service->name,
            'service_prefix' => $service->prefix,
            'serving' => $serving ? [
                'number' => ClientQueues::formatNumber($serving->queue_number, $service->prefix),
                'name' => $serving->name,
            ] : null,
            'next' => $next->map(fn ($q) => ClientQueues::formatNumber($q->queue_number, $service->prefix))->values(),
        ]);
    }

}
