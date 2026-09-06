<?php

namespace App\Services;

use App\Models\ClientQueues;
use App\Models\Service;
use Illuminate\Support\Collection;

/**
 * Shapes a service's "currently serving" + "up next" data for the
 * waiting-room display boards.
 *
 * Used by both the all-services board and each department's own dedicated
 * screen — previously duplicated almost line-for-line between
 * DisplayAllQueueController::servingPatients() and ::servingPatient().
 */
class DisplayBoardService
{
    /**
     * One board entry for the given service. Works whether or not
     * `clientQueues`/`waitingQueues` were eager-loaded: the all-services
     * board preloads both (one query for everyone), while the single
     * department display queries fresh each time — either way this just
     * reads through to whatever's already on the relation.
     */
    public function boardFor(Service $service): array
    {
        $serving = $service->relationLoaded('clientQueues')
            ? $service->clientQueues->first()
            : $service->clientQueues()->where('status', 'serving')->orderBy('queue_number')->first();

        $next = $service->relationLoaded('waitingQueues')
            ? $service->waitingQueues
            : $service->waitingQueues()->limit(3)->get();

        return [
            'service_name' => $service->name,
            'service_prefix' => $service->prefix,
            'serving' => $serving ? [
                'number' => ClientQueues::formatNumber($serving->queue_number, $service->prefix),
                'name' => $serving->name,
            ] : null,
            // Numbers only, on purpose — the display never shows a
            // waiting visitor's name, only the person at the counter.
            'next' => $next->map(fn ($q) => ClientQueues::formatNumber($q->queue_number, $service->prefix))->values(),
        ];
    }

    /**
     * A board entry for every public-facing service (the internal "admin"
     * bucket excluded), for the all-services display.
     */
    public function allBoards(): Collection
    {
        $services = Service::publicFacing()
            ->with(['clientQueues' => function ($query) {
                $query->where('status', 'serving')->orderBy('queue_number')->limit(1);
            }, 'waitingQueues' => function ($query) {
                $query->limit(3);
            }])
            ->get();

        return $services->map(fn (Service $service) => $this->boardFor($service));
    }
}
