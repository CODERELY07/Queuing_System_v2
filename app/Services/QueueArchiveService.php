<?php

namespace App\Services;

use App\Models\ClientQueues;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Everything about the admin queues page that isn't the plain index/destroy
 * resource actions: bulk-archiving old tickets, browsing the archive, and
 * restoring/permanently deleting from it.
 */
class QueueArchiveService
{
    public function archivedCount(): int
    {
        return ClientQueues::onlyTrashed()->count();
    }

    /**
     * Soft delete every queue from before today. Soft rather than hard so
     * the admin analytics (daily trend, finished/skipped totals, etc.)
     * still have the underlying rows to add up — the tickets just drop out
     * of the active list and today's counters, which already only ever
     * looked at today anyway.
     */
    public function deleteOld(): int
    {
        return ClientQueues::whereDate('created_at', '<', now()->toDateString())->delete();
    }

    /**
     * The soft-deleted tickets "Delete Old Queues" and the per-row Delete
     * button have set aside — kept out of the active list, but browsable
     * and recoverable here instead of just gone.
     */
    public function paginateArchived(?string $search, ?string $sort, ?string $dir, int $perPage = 10): LengthAwarePaginator
    {
        return ClientQueues::onlyTrashed()
            ->with('service')
            ->search($search)
            ->sortBy($sort ?? 'deleted_at', $dir ?? 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Bring an archived ticket back onto the active queues list.
     */
    public function restore(int $id): ClientQueues
    {
        $queue = ClientQueues::onlyTrashed()->findOrFail($id);
        $queue->restore();

        return $queue;
    }

    /**
     * Permanently erase an archived ticket — the one place left that's
     * truly irreversible, and only reachable from the archive itself, not
     * the active list.
     */
    public function purge(int $id): void
    {
        ClientQueues::onlyTrashed()->findOrFail($id)->forceDelete();
    }
}
