<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\DisplayBoardService;

class DisplayAllQueueController extends Controller
{
    public function __construct(private readonly DisplayBoardService $boards)
    {
    }

    public function index()
    {
        // Services listed here too, so the all-services board can link out
        // to each department's own dedicated full-screen display.
        $services = Service::publicFacing()->get();

        return view('display.counter', compact('services'));
    }

    public function servingPatients()
    {
        return response()->json($this->boards->allBoards());
    }

    /**
     * The dedicated, full-screen display for a single department.
     */
    public function show(Service $service)
    {
        abort_if($service->is_internal, 404);

        return view('display.show', compact('service'));
    }

    /**
     * Same shape as one entry of servingPatients(), just scoped to the one
     * service the dedicated display page is polling for.
     */
    public function servingPatient(Service $service)
    {
        return response()->json($this->boards->boardFor($service));
    }
}
