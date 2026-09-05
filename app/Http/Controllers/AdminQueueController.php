<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use Illuminate\Http\Request;

class AdminQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $queues = ClientQueues::with('service')
            ->search(request('q'))
            ->sortBy(request('sort'), request('dir'))
            ->paginate(5)
            ->withQueryString();

        $today = ClientQueues::whereDate('created_at', now());
        $waitingToday = (clone $today)->where('status', 'waiting')->count();
        $skippedToday = (clone $today)->where('status', 'skipped')->count();
        $finishedToday = (clone $today)->where('status', 'finish')->count();

        return view('admin.queues', compact('queues', 'waitingToday', 'skippedToday', 'finishedToday'));
    }

    /**
     * Remove the specified resource from storage.
        */
    public function destroy($id)
    {
        $queue = ClientQueues::findOrFail($id);

        if ($queue->created_at->isSameDay(now())) {
            return redirect()->back()
                ->with('error', 'You can only delete queues from previous days This Queue is New.');
        }

        $queue->delete();

        return redirect()->back()
            ->with('success', 'Queue deleted successfully.');
    }
    public function deleteOld()
    {
        $deleted = ClientQueues::whereDate('created_at', '<', now()->toDateString())->delete();

        return redirect()->back()
            ->with('success', $deleted > 0 
                ? 'Queues from previous days have been deleted.' 
                : 'No old queues to delete, All Queue is New.');
    }
}
