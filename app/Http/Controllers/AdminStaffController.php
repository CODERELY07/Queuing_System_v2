<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminStaffController extends Controller
{
        public function index(){
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

    public function store(Request $request){
        try {
            $staff = $this->validateStaff($request);
            $staff['password'] = Hash::make($staff['password']);
            $staff['user_type'] = 'staff';

            User::create($staff);

            return $this->jsonSuccess('Staff Added Successfully!');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $this->validateStaff($request, $id);

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $updated = User::where('id', $id)->update($validated);

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
            $deleted = User::destroy($id);

            return $deleted
                ? $this->jsonSuccess('Staff deleted successfully.')
                : $this->jsonError('Delete failed.');
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage());
        }
    }

    /**
     * Validation rules shared by create and update. Pass $id to make the
     * uniqueness checks ignore that record and make the password optional.
     */
    private function validateStaff(Request $request, $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:50|unique:users,name' . ($id ? ",{$id}" : ''),
            'email' => 'required|email|unique:users,email' . ($id ? ",{$id}" : ''),
            'service_id' => 'required|integer|exists:services,id',
            'password' => $id ? 'nullable|string|confirmed' : 'required|string|confirmed',
        ]);
    }

}
