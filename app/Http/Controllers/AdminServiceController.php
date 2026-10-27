<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminServiceController extends Controller
{
    public function index(): View
    {
        // Counts drive the delete guard below — a service still tied to
        // staff or queue history can't be removed, since both foreign keys
        // cascade-delete and that would silently take the analytics down
        // with it.
        $services = Service::withCount([
                'users',
                'clientQueues as tickets_count' => fn ($query) => $query->withTrashed(),
            ])
            ->orderBy('name')
            ->get();

        return view('admin.services', compact('services'));
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        Service::create($request->validated());

        return $this->jsonSuccess('Service added.');
    }

    public function update(UpdateServiceRequest $request, int $id): JsonResponse
    {
        // A model instance (not a query-builder mass update) so the
        // slug-regeneration hook on Service actually fires.
        $service = Service::findOrFail($id);

        // The internal "Admin" bucket is a system fixture — renaming it
        // would break the lookup that gives admin accounts their service.
        if ($service->is_internal) {
            return $this->jsonError('This is a protected system service and can\'t be edited.');
        }

        $service->update($request->validated());

        // wasChanged(), not update()'s return value: that is true even
        // when nothing differed.
        return $this->jsonSuccess($service->wasChanged() ? 'Service updated.' : 'No changes to save.');
    }

    public function destroy(int $id): JsonResponse
    {
        $service = Service::withCount([
                'users',
                'clientQueues as tickets_count' => fn ($query) => $query->withTrashed(),
            ])
            ->findOrFail($id);

        // Belt-and-braces beyond the users_count check below: that one
        // only holds as long as at least one admin account still points at
        // this service. This one holds regardless.
        if ($service->is_internal) {
            return $this->jsonError('This is a protected system service and can\'t be deleted.');
        }

        if ($service->users_count > 0) {
            return $this->jsonError('Reassign or remove this service\'s staff before deleting it.');
        }

        if ($service->tickets_count > 0) {
            return $this->jsonError('This service has queue history and can\'t be deleted — its records are kept for reporting.');
        }

        $service->delete();

        return $this->jsonSuccess('Service deleted.');
    }
}
