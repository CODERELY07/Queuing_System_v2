<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use App\Models\User;
use App\Services\QueueStatsService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(private readonly QueueStatsService $stats)
    {
    }

    public function index($user_type)
    {
        if (auth()->user()->user_type !== $user_type) {
            abort(403);
        }

        $user = User::where('id', Auth::id())->with('service')->first();

        if ($user_type === 'staff') {
            $queues = ClientQueues::where('service_id', $user->service_id)
                ->search(request('q'))
                ->sortBy(request('sort'), request('dir'))
                ->paginate(5)
                ->withQueryString();

            $counts = $this->stats->todayCounts($user->service_id);

            return view('staff.dashboard', [
                'user' => $user,
                'queues' => $queues,
                'waitingToday' => $counts['waiting'],
                'skippedToday' => $counts['skipped'],
                'finishedToday' => $counts['finished'],
            ]);
        }

        if ($user_type === 'admin') {
            $activeStaffCount = User::where('user_type', 'staff')
                ->where('last_seen', '>=', now()->subMinutes(5))
                ->count();
            $activeQueue = ClientQueues::where('status', 'serving')->count();
            $priorityWaiting = ClientQueues::where('status', 'waiting')->where('priority', true)->count();

            return view('admin.dashboard', array_merge(
                compact('activeStaffCount', 'activeQueue', 'priorityWaiting'),
                $this->stats->dashboardAnalytics()
            ));
        }

        return view('kiosk.index');
    }
}
