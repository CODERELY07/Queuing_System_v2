<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
      public function data()
    {
        $service = Auth::user()->service_id;
        $recent = ClientQueues::where('status', 'waiting')->where('service_id', $service)->take(3)->get()->map(function ($q) {
            return [
                'number' => $q->service->prefix . '-' . str_pad($q->queue_number, 3, '0', STR_PAD_LEFT),
                'status' => ucfirst($q->status),
            ];
        });

        $waitingCount = ClientQueues::where('status', 'waiting')->where('service_id', $service)->count();

        $servingPatient = ClientQueues::where('status', 'serving')
        ->where('service_id', $service)
        ->orderBy('queue_number')
        ->first();

        
        if ($servingPatient) {
            $formatted = [
                'number' => $servingPatient->service->prefix . '-' . str_pad($servingPatient->queue_number, 3, '0', STR_PAD_LEFT),
                'name' => ucfirst($servingPatient->name),
            ];
        } else {
            if($waitingCount > 0){
                $formatted = [
                    'number' => "Start Queue",
                    'name' => "Patient is waiting...",
                ];
            }else{
                 $formatted = [
                    'number' => "No Queue",
                    'name' => "No work",
                ];
            }
           
        }

        return response()->json([
            'waiting' => $waitingCount,
            'recent' => $recent,
            'servingPatient' => $formatted
        ]);
    }

    public function callNext()
    {
        $service = Auth::user()->service_id;

        $waiting = ClientQueues::where('status', 'waiting')
            ->where('service_id', $service)
            ->orderBy('queue_number')
            ->first();

        $next = ClientQueues::where('status', 'serving')
            ->where('service_id', $service)
            ->orderBy('queue_number')
            ->first();
    
        if (!empty($waiting) || !empty($next)) {
             $waiting?->update(['status' => 'serving']);
            $next?->update(['status' => 'finish']);

            $active = $waiting ?? $next; 
            $number = $active->service->prefix . '-' . str_pad($active->queue_number, 3, '0', STR_PAD_LEFT);
        } else {
            $number = 'None';
        }

        return response()->json(['number' => $number]);
    }

    public function callPrevious()
    {
        $serviceId = Auth::user()->service_id;

        $current = ClientQueues::where('status', 'serving')
            ->where('service_id', $serviceId)
            ->first();

        $previous = ClientQueues::where('service_id', $serviceId)
            ->where('status', 'finish')
            ->orderBy('queue_number', 'desc')
            ->first();
        
        if ($previous) {
            $current?->update(['status' => 'waiting']);
            $previous?->update(['status' => 'serving']);

            $number = $previous->service->prefix . '-' . str_pad($previous->queue_number, 3, '0', STR_PAD_LEFT);
        } else {
            $number = 'None';
        }

        return response()->json(['number' => $number]);
    }


}
