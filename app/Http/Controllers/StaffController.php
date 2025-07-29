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

        $waitingPatient = ClientQueues::where('status', 'waiting')
        ->where('service_id', $service)
        ->orderBy('queue_number')
        ->first();

        if ($waitingPatient) {
            $formatted = [
                'number' => $waitingPatient->service->prefix . '-' . str_pad($waitingPatient->queue_number, 3, '0', STR_PAD_LEFT),
                'name' => ucfirst($waitingPatient->name),
            ];
        } else {
            $formatted = null;
        }

        return response()->json([
            'waiting' => $waitingCount,
            'recent' => $recent,
            'waitingPatient' => $formatted
        ]);
    }

    public function callNext()
    {
        $service = Auth::user()->service_id;

        $next = ClientQueues::where('status', 'waiting')
            ->where('service_id', $service)
            ->orderBy('queue_number')
            ->first();

        if ($next) {
            // Update status to 'serving'
            $next->update(['status' => 'serving']);

            // Format the number (e.g., SVC-001)
            $number = $next->service->prefix . '-' . str_pad($next->queue_number, 3, '0', STR_PAD_LEFT);
        } else {
            $number = 'None';
        }

        return response()->json(['number' => $number]);
    }

}
