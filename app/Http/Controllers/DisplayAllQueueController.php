<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class DisplayAllQueueController extends Controller
{
    public function index(){
        return view('display.counter');
    }
    
    public function servingPatients()
    {
        $services = Service::where('name', '!=', 'admin')
        ->with(['clientQueues' => function ($query) {
            $query->where('status', 'serving')
                ->orderBy('queue_number')
                ->limit(1);
        }])
        ->get();


        $result = $services->map(function ($service) {
            $serving = $service->clientQueues->first();
            return [
                'service_name' => $service->name,
                'service_prefix' => $service->prefix,
                'serving' => $serving ? [
                    'number' => $service->prefix . '-' . str_pad($serving->queue_number, 3, '0', STR_PAD_LEFT),
                    'name' => $serving->name,
                ] : null,
            ];
        });

        return response()->json($result);
    }

}
