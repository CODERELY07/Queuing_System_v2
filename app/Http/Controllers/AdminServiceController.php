<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Database\QueryException;

class AdminServiceController extends Controller
{
    public function index()
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

    public function store(StoreServiceRequest $request)
    {
        try {
            $data = $request->validated();
            $data['prefix'] = strtoupper($data['prefix']);

            Service::create($data);

            return $this->jsonSuccess('Service added successfully!');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage());
        }
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $validated['prefix'] = strtoupper($validated['prefix']);

            // A model instance (not a query-builder mass update) so the
            // slug-regeneration hook on Service actually fires.
            $service = Service::findOrFail($id);
            $updated = $service->update($validated);

            return $updated
                ? $this->jsonSuccess('Service updated successfully.')
                : $this->jsonError('No changes were made.');
        } catch (QueryException $e) {
            return $this->jsonError('Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->jsonError('Unexpected error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::withCount([
                    'users',
                    'clientQueues as tickets_count' => fn ($query) => $query->withTrashed(),
                ])
                ->findOrFail($id);

            // Belt-and-braces beyond the users_count check below: that one
            // only holds as long as at least one admin account still
            // points at this service. This one holds regardless.
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

            return $this->jsonSuccess('Service deleted successfully.');
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage());
        }
    }
}
