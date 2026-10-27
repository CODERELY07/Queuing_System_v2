<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(): View
    {
        $staffs = User::where('user_type', 'staff')
            ->with('service')
            ->search(request('q'))
            ->when(
                in_array(request('sort'), ['name', 'email'], true),
                fn ($query) => $query->orderBy(request('sort'), request('dir') === 'desc' ? 'desc' : 'asc'),
                fn ($query) => $query->latest(),
            )
            ->get();

        // Real departments only — the internal "Admin" bucket isn't a
        // counter anyone can be assigned to.
        $services = Service::publicFacing()->orderBy('name')->get();

        return view('admin.staff', compact('staffs', 'services'));
    }

    public function store(StoreStaffRequest $request): JsonResponse
    {
        // The 'hashed' cast on User hashes the password; user_type isn't
        // in the request, so it's set here and can't be spoofed by it.
        User::create($request->validated() + ['user_type' => 'staff']);

        return $this->jsonSuccess('Staff member added.');
    }

    public function update(UpdateStaffRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        // A blank password on edit means "keep the current one".
        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        // Scoped to staff: this endpoint is reached with a plain numeric id
        // from the staff table, and without the scope nothing stopped it
        // being pointed at an admin account (its own included) to silently
        // change that account's password instead. A model instance, so the
        // 'hashed' cast applies to a new password.
        $staff = User::where('user_type', 'staff')->findOrFail($id);
        $staff->update($validated);

        return $this->jsonSuccess($staff->wasChanged() ? 'Staff member updated.' : 'No changes to save.');
    }

    public function destroy(int $id): JsonResponse
    {
        // Same reasoning as update(): scope to staff so this can never be
        // used to delete an admin account by id, the caller's own included.
        User::where('user_type', 'staff')->findOrFail($id)->delete();

        return $this->jsonSuccess('Staff member deleted.');
    }
}
