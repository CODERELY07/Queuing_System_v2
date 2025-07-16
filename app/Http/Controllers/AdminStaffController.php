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
        $staffs = User::where('user_type', 'staff')->with('service')->get();
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
                    return redirect()->route('admin.staff')->with(['success' => 'Staff added successfully!']);
                } else {
                    dd('Insert failed');
                }
            } catch (QueryException $e) {
                dd('Database Error: ' . $e->getMessage());
            } catch (\Exception $e) {
                dd('General Error: ' . $e->getMessage());
            }
    }
    public function update($id){
        
    }

    private function validateStaff($request) {
        return $request->validate([
            'name' => 'string|required|max:50|unique:users,name',
            'email' => 'email|required|unique:users,email',
            'service_id' => 'required|exists:services,id|integer',
            'password' => 'required|string|confirmed',
        ]);
    }

}
