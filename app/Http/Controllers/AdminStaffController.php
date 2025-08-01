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
            $staffs = User::where('user_type', 'staff')->with('service')->latest()->get();
            $services = Service::with('users')->get();
            return view('admin.staff', compact('staffs', 'services'));
        }

    public function store(Request $request){
        try {
                $staff = $this->validateStaff($request);
                $staff['password'] = Hash::make($staff['password']);
                $staff['user_type'] = 'staff';

                $create = User::create($staff);

                if ($create) {
                    return response()->json(['status' => 'success','message' => 'Staff Added Successfully!']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Insert Failed!']);
                }
            } catch (QueryException $e) {
                   return response()->json(['status' => 'error', 'message' =>  $e->getMessage()]);
            } catch (\Exception $e) {
                  return response()->json(['status' => 'error', 'message' =>  $e->getMessage()]);
            }
    }
    public function update(Request $request, $id)
    {
        // return response()->json(['status' => 'success', 'message' => 'response']);
        try {
            $validated = $this->validateStaffUpdate($request, $id);

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $updated = User::where('id', $id)->update($validated);

            if ($updated) {
                return response()->json(['status' => 'success', 'message' => 'Staff updated successfully.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No changes were made.']);
            }
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Unexpected error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = User::destroy($id);
            if ($deleted) {
                return response()->json(['status' => 'success', 'message' => 'Staff deleted successfully.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Delete failed.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function validateStaff($request) {
        return $request->validate([
            'name' => 'string|required|max:50|unique:users,name',
            'email' => 'email|required|unique:users,email',
            'service_id' => 'required|exists:services,id|integer',
            'password' => 'required|string|confirmed',
        ]);
    }

    private function validateStaffUpdate($request, $id) {
        return $request->validate([
            'name' => 'string|required|max:50|unique:users,name,' . $id,
            'email' => 'email|required|unique:users,email,' . $id,
            'service_id' => 'required|exists:services,id|integer',
            'password' => 'nullable|string|confirmed',
        ]);
    }

}
