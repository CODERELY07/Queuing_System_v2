<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\Service;
use App\Models\User;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('user_type', 'staff')
            ->with('service')
            ->when(request('q'), fn ($q) => $q->where(function ($q) {
                $term = '%' . request('q') . '%';
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            }))
            ->when(
                in_array(request('sort'), ['name', 'email']),
                fn ($q) => $q->orderBy(request('sort'), request('dir') === 'desc' ? 'desc' : 'asc'),
                fn ($q) => $q->latest(),
            )
            ->get();
        $services = Service::with('users')->get();
        return view('admin.staff', compact('staffs', 'services'));
    }

    public function store(StoreStaffRequest $request)
    {
        try {
            $staff = $request->validated();
            $staff['password'] = Hash::make($staff['password']);
            $staff['user_type'] = 'staff';

            User::create($staff);

            return $this->jsonSuccess('Staff Added Successfully!');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage());
        }
    }

    public function update(UpdateStaffRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Scoped to user_type='staff' — this endpoint is reached with a
            // plain numeric id from the staff table, and without the scope
            // nothing stopped it being pointed at an admin account (its own
            // included) to silently change that account's password instead.
            $updated = User::where('user_type', 'staff')->where('id', $id)->update($validated);

            return $updated
                ? $this->jsonSuccess('Staff updated successfully.')
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
            // Same reasoning as update(): scope to staff so this can never
            // be used to delete an admin account by id, including the
            // caller's own.
            $deleted = User::where('user_type', 'staff')->where('id', $id)->delete();

            return $deleted
                ? $this->jsonSuccess('Staff deleted successfully.')
                : $this->jsonError('Delete failed.');
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage());
        }
    }
}
