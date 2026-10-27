<?php

namespace App\Services;

use App\Models\ClientQueues;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * The ticket table on a staff member's dashboard. One definition shared by
 * the full page (UserController) and the small HTML partial that
 * counter.js swaps in live (StaffController::queueTable), so the two can
 * never disagree about what's on a page.
 */
class StaffQueueService
{
    private const PER_PAGE = 10;

    public function paginate(int $serviceId, Request $request): LengthAwarePaginator
    {
        $query = ClientQueues::forService($serviceId)
            ->today()
            ->search($request->query('q'));

        // An explicit column sort wins; otherwise the work in front of the
        // counter comes first (see ClientQueues::scopeWorkOrder).
        $query = $request->filled('sort')
            ? $query->sortBy($request->query('sort'), $request->query('dir'))
            : $query->workOrder();

        return $query->paginate(self::PER_PAGE)
            ->withQueryString()
            // Pagination links must point at the dashboard page even when
            // this runs for the live-refresh partial's own URL.
            ->withPath(route('dashboard.with_type', ['user_type' => 'staff']));
    }
}
