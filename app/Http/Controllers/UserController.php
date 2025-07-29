<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        $id = Auth::user()->id;
        $user = User::where('id', $id)->with('service')->first();

        if(Auth::check()){
            if(Auth::user()->user_type == "staff"){
                return view('staff.dashboard', compact('user'));
            }else if(Auth::user()->user_type == 'admin'){
                return view('admin.dashboard');
            }else{
                return view('kiosk.index');
            }
        }
    }
}
