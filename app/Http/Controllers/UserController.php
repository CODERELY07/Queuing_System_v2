<?php

namespace App\Http\Controllers;

use App\Models\ClientQueues;
use App\Models\User;
use App\Services\QueueStatsService;
use App\Services\StaffQueueService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly QueueStatsService $stats,
        private readonly StaffQueueService $tickets,
    ) {
    }

    /**
     * The signed-in user's own dashboard. The route constrains {user_type}
     * to admin|staff; a valid-but-different type (a staff member typing
     * /dashboard/admin) is a 403, not someone else's page.
     */
    public function index(Request $request, string $user_type): View
    {
        $user = $request->user();

        abort_unless($user->user_type === $user_type, 403);

        return $user_type === 'admin'
            ? $this->adminDashboard()
            : $this->staffDashboard($request, $user->load('service'));
    }

    private function staffDashboard(Request $request, User $user): View
    {
        $counts = $this->stats->todayCounts($user->service_id);

        return view('staff.dashboard', [
            'user' => $user,
            'queues' => $this->tickets->paginate($user->service_id, $request),
            'waitingToday' => $counts['waiting'],
            'skippedToday' => $counts['skipped'],
            'finishedToday' => $counts['finished'],
        ]);
    }

    private function adminDashboard(): View
    {
        // Today's queue, like every other live figure on this page —
        // a ticket nobody closed out yesterday isn't "active" now.
        $activeQueue = ClientQueues::today()->where('status', 'serving')->count();
        $priorityWaiting = ClientQueues::today()->where('status', 'waiting')->where('priority', true)->count();

        $activeStaffCount = User::where('user_type', 'staff')
            ->where('last_seen', '>=', now()->subMinutes(5))
            ->count();

        return view('admin.dashboard', array_merge(
            compact('activeQueue', 'priorityWaiting', 'activeStaffCount'),
            $this->stats->dashboardAnalytics(),
        ));
    }
}
