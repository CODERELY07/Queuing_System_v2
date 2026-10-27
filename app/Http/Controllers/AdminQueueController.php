<?php

namespace App\Http\Controllers;

use App\Models\ClientQueues;
use App\Services\QueueArchiveService;
use App\Services\QueueStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminQueueController extends Controller
{
    public function __construct(
        private readonly QueueArchiveService $archive,
        private readonly QueueStatsService $stats,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $queues = ClientQueues::with('service')
            ->search(request('q'))
            ->sortBy(request('sort'), request('dir'))
            ->paginate(10)
            ->withQueryString();

        $counts = $this->stats->todayCounts();

        return view('admin.queues', [
            'queues' => $queues,
            'waitingToday' => $counts['waiting'],
            'skippedToday' => $counts['skipped'],
            'finishedToday' => $counts['finished'],
            'archivedCount' => $this->archive->archivedCount(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * Soft deletes, same as the bulk "Delete Old Queues" sweep below — a
     * misclick here used to be permanent with no way back. Now it just
     * drops off the active list and can be recovered from the archive
     * (see archived()/restore()) if it was a mistake.
     */
    public function destroy(int $id): RedirectResponse
    {
        $queue = ClientQueues::findOrFail($id);

        if ($queue->created_at->isSameDay(now())) {
            return redirect()->back()
                ->with('error', 'Only tickets from previous days can be deleted — this one is from today.');
        }

        $queue->delete();

        return redirect()->back()
            ->with('success', 'Queue moved to the archive. You can restore it from there if this was a mistake.');
    }

    /**
     * Soft delete every queue from before today.
     */
    public function deleteOld(): RedirectResponse
    {
        $deleted = $this->archive->deleteOld();

        return redirect()->back()
            ->with('success', $deleted > 0
                ? 'Queues from previous days have been moved to the archive.'
                : 'There are no old queues to delete — every ticket is from today.');
    }

    /**
     * The soft-deleted tickets "Delete Old Queues" and the per-row Delete
     * button have set aside — kept out of the active list, but browsable
     * and recoverable here instead of just gone.
     */
    public function archived(): View
    {
        $queues = $this->archive->paginateArchived(request('q'), request('sort'), request('dir'));

        return view('admin.queues-archive', compact('queues'));
    }

    /**
     * Bring an archived ticket back onto the active queues list.
     */
    public function restore(int $id): RedirectResponse
    {
        $this->archive->restore($id);

        return redirect()->back()->with('success', 'Queue restored to the active list.');
    }

    /**
     * Permanently erase an archived ticket.
     */
    public function purge(int $id): RedirectResponse
    {
        $this->archive->purge($id);

        return redirect()->back()->with('success', 'Queue permanently deleted.');
    }
}
