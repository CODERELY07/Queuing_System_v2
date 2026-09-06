<?php

use App\Http\Controllers\AdminQueueController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\ClientQueueController;
use App\Http\Controllers\DisplayAllQueueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Real, admin-configured services rather than a hardcoded list, so the
    // homepage never drifts out of sync with what the kiosk actually offers.
    $services = Service::publicFacing()->get();
    return view('home', compact('services'));
})->name('home');

// Dashboard
Route::get('/dashboard/{user_type}', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');Route::get('/dashboard', function () {
    $user = Auth::user();
    return redirect()->route('dashboard.with_type', ['user_type' => $user->user_type]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/dashboard/{user_type}', [UserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.with_type');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Display Routes
Route::prefix('display')->group(function () {
    Route::get('/', [DisplayAllQueueController::class, 'index'])->name('display');
    Route::get('/serving-patients', [DisplayAllQueueController::class, 'servingPatients']);

    // One big, dedicated screen per department (e.g. /display/2 for
    // Doctor Consultation) — for mounting at that department's own
    // waiting area, rather than everyone sharing the all-services board.
    // Must stay after the literal /serving-patients route above, or this
    // wildcard would swallow it first.
    Route::get('/{service:slug}', [DisplayAllQueueController::class, 'show'])->name('display.show');
    Route::get('/{service:slug}/serving-patient', [DisplayAllQueueController::class, 'servingPatient'])->name('display.show.data');
});

// Staff Routes
//
// These actually move a live queue (call/skip/recall) and used to be
// reachable with no login at all. `auth` closes that; `user_type:staff`
// stops a logged-in *admin* from hitting them too (both roles carry a
// service_id — admin's is the internal "Admin" bucket — so nothing else
// would have stopped that).
Route::prefix('staff')->middleware(['auth', 'verified', 'user_type:staff'])->group(function () {
    Route::post('/call-next', [StaffController::class, 'callNext'])->name('staff.call-next');
    Route::post('/call-previous', [StaffController::class, 'callPrevious']);
    Route::post('/call', [StaffController::class, 'call']);
    Route::post('/call/{id}', [StaffController::class, 'selectedCall']);
    Route::post('/skip', [StaffController::class, 'skip'])->name('staff.skip');
    Route::get('/dashboard-data', [StaffController::class, 'data']);
});


// Admin Routes
//
// `auth` alone only proved *someone* was logged in — nothing checked that
// they were actually an admin, so a staff account could reach every one of
// these by just knowing the URL. `user_type:admin` closes that.
Route::prefix('admin')->middleware(['auth', 'verified', 'user_type:admin'])->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('admin');

    // Only index/destroy are implemented on the controller — leaving this
    // as a full resource() registered create/show/edit/update routes to
    // nothing and every one of them 500'd with "method does not exist"
    // if actually hit.
    Route::resource('queues', AdminQueueController::class)->only(['index', 'destroy'])->names([
        'index' => 'admin.queues',
    ]);

    Route::delete('/delete-old', [AdminQueueController::class, 'deleteOld'])->name('queues.deleteOld');
    Route::get('/queues-archive', [AdminQueueController::class, 'archived'])->name('queues.archived');
    Route::post('/queues/{id}/restore', [AdminQueueController::class, 'restore'])->name('queues.restore');
    Route::delete('/queues/{id}/purge', [AdminQueueController::class, 'purge'])->name('queues.purge');

     // CRUD
    Route::controller(App\Http\Controllers\AdminStaffController::class)->group(function () {
        Route::get('/staff', 'index')->name('admin.staff');
        Route::post('/store', 'store')->name('staff.store');
        Route::put('/staff/update/{id}', 'update')->name('staff.update');
        Route::delete('/staff/destroy/{id}', 'destroy')->name('staff.destroy');
    });

    Route::controller(AdminServiceController::class)->group(function () {
        Route::get('/services', 'index')->name('admin.services');
        Route::post('/services', 'store')->name('services.store');
        Route::put('/services/{id}', 'update')->name('services.update');
        Route::delete('/services/{id}', 'destroy')->name('services.destroy');
    });
});

// Queues Route
Route::prefix('kiosk')->name('kiosk')->group(function () {
    Route::controller(ClientQueueController::class)->group(function () {
        Route::get('/', 'index');
        // Route::get('/ticket', 'kiosk.ticket')->name('.ticket');
        Route::post('/', 'store')->name('.store');  
    });
});





require __DIR__.'/auth.php';
