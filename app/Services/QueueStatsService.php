<?php

namespace App\Services;

use App\Models\ClientQueues;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Aggregate, read-only numbers for the admin and staff dashboards.
 *
 * Pulled out of UserController/AdminQueueController because the same
 * "waiting/skipped/finished today" counts were being hand-copied in three
 * places (admin queues page, staff dashboard, admin dashboard analytics)
 * with slightly different scoping each time.
 */
class QueueStatsService
{
    /**
     * Waiting/skipped/finished/total counts for today, optionally scoped
     * to one service (the staff dashboard) — omit for system-wide (admin).
     *
     * @return array{waiting: int, skipped: int, finished: int, total: int}
     */
    public function todayCounts(?int $serviceId = null): array
    {
        $today = ClientQueues::whereDate('created_at', now())
            ->when($serviceId, fn ($query) => $query->where('service_id', $serviceId));

        return [
            'waiting' => (clone $today)->where('status', 'waiting')->count(),
            'skipped' => (clone $today)->where('status', 'skipped')->count(),
            'finished' => (clone $today)->where('status', 'finish')->count(),
            'total' => (clone $today)->count(),
        ];
    }

    /**
     * Average minutes from ticket creation to being marked finished, for
     * tickets finished today — a proxy for turnaround since there's no
     * separate "serving started" timestamp on the queue row.
     *
     * The minute-difference expression isn't portable SQL — MySQL has
     * TIMESTAMPDIFF(), Postgres doesn't — so it's picked per driver rather
     * than assuming MySQL.
     */
    public function avgTurnaroundMinutesToday(): ?float
    {
        $diffInMinutes = match (DB::connection()->getDriverName()) {
            'pgsql' => 'EXTRACT(EPOCH FROM (updated_at - created_at)) / 60',
            default => 'TIMESTAMPDIFF(MINUTE, created_at, updated_at)',
        };

        return ClientQueues::whereDate('created_at', now())
            ->where('status', 'finish')
            ->avg(DB::raw($diffInMinutes));
    }

    /**
     * Finished vs. no-show counts for each of the last 7 days (today
     * included). Reads `withTrashed()` — "Delete Old Queues" only soft
     * deletes, precisely so this history stays intact instead of
     * disappearing the moment an admin clears the active list.
     */
    public function weeklyTrend(): Collection
    {
        $rangeStart = now()->subDays(6)->startOfDay();

        $trendRows = ClientQueues::withTrashed()
            ->whereBetween('created_at', [$rangeStart, now()->endOfDay()])
            ->selectRaw('DATE(created_at) as day, status, COUNT(*) as total')
            ->groupBy('day', 'status')
            ->get()
            ->groupBy('day');

        return collect(range(0, 6))->map(function ($daysAgo) use ($trendRows) {
            $date = now()->subDays(6 - $daysAgo);
            $rows = $trendRows->get($date->toDateString(), collect());

            return [
                'label' => $date->format('D'),
                'finished' => (int) $rows->firstWhere('status', 'finish')?->total,
                'skipped' => (int) $rows->firstWhere('status', 'skipped')?->total,
                'total' => (int) $rows->sum('total'),
            ];
        });
    }

    /**
     * The service with the most tickets today, or null on a quiet day.
     * Also `withTrashed()`, for the same reason as weeklyTrend().
     */
    public function busiestServiceToday(): ?ClientQueues
    {
        return ClientQueues::withTrashed()
            ->whereDate('created_at', now())
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->with('service')
            ->first();
    }

    /**
     * Everything the admin dashboard's "Queueing Analytics" panel needs,
     * bundled into one call.
     */
    public function dashboardAnalytics(): array
    {
        $today = $this->todayCounts();

        return [
            'totalToday' => $today['total'],
            'finishedToday' => $today['finished'],
            'skippedToday' => $today['skipped'],
            'avgTurnaroundMinutesToday' => $this->avgTurnaroundMinutesToday(),
            'weeklyTrend' => $this->weeklyTrend(),
            'busiestService' => $this->busiestServiceToday(),
        ];
    }
}
