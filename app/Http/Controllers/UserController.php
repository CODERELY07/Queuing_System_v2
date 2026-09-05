<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientQueues;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index($user_type){
        if (auth()->user()->user_type !== $user_type) {
            abort(403); 
        }
        $id = Auth::user()->id;
        $user = User::where('id', $id)->with('service')->first();

        if(Auth::check()){
            if(Auth::user()->user_type == "staff"){
                $queues = ClientQueues::where('service_id', $user->service_id)
                    ->search(request('q'))
                    ->sortBy(request('sort'), request('dir'))
                    ->paginate(5)
                    ->withQueryString();

                $today = ClientQueues::where('service_id', $user->service_id)->whereDate('created_at', now());
                $waitingToday = (clone $today)->where('status', 'waiting')->count();
                $skippedToday = (clone $today)->where('status', 'skipped')->count();
                $finishedToday = (clone $today)->where('status', 'finish')->count();

                return view("{$user_type}.dashboard", compact('user', 'queues', 'waitingToday', 'skippedToday', 'finishedToday'));
            }else if(Auth::user()->user_type == 'admin'){
                $activeStaffCount = User::where('user_type', 'staff')
                            ->where('last_seen', '>=', now()->subMinutes(5))
                            ->count();
                $activeQueue = ClientQueues::where('status', 'serving')->count();
                $priorityWaiting = ClientQueues::where('status', 'waiting')->where('priority', true)->count();
                return view("{$user_type}.dashboard",compact('activeStaffCount', 'activeQueue', 'priorityWaiting'));
            }else{
                return view('kiosk.index');
            }
        }
    }
}
