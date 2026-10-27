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
//
// `dashboard` is the stable, type-less entry point (Breeze's redirects and
// tests use it); `dashboard.with_type` is the real page. They used to be
// registered on a single line with the *same name*, and the typed path was
// registered twice — so route('dashboard', [...]) resolved to whichever won
// and every sidebar click took an extra redirect hop.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard.with_type', ['user_type' => Auth::user()->user_type]))
        ->name('dashboard');

    Route::get('/dashboard/{user_type}', [UserController::class, 'index'])
        ->whereIn('user_type', ['admin', 'staff'])
        ->name('dashboard.with_type');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware('protect_demo')->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware('protect_demo')->name('profile.destroy');
});

// Display Routes
Route::prefix('display')->group(function () {
    Route::get('/', [DisplayAllQueueController::class, 'index'])->name('display');
    Route::get('/serving-patients', [DisplayAllQueueController::class, 'servingPatients'])->name('display.data');

    // One big, dedicated screen per department (e.g. /display/doctor-
    // consultation) — for mounting at that department's own waiting
    // area, rather than everyone sharing the all-services board.
    // Must stay after the literal /serving-patients route above, or this
    // wildcard would swallow it first (Service::uniqueSlug() reserves that
    // slug for the same reason).
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
Route::prefix('staff')->middleware(['auth', 'verified', 'user_type:staff'])->controller(StaffController::class)->group(function () {
    Route::post('/call-next', 'callNext')->name('staff.call-next');
    Route::post('/call-previous', 'callPrevious')->name('staff.call-previous');
    Route::post('/call', 'call')->name('staff.call');
    Route::post('/call/{id}', 'selectedCall')->whereNumber('id')->name('staff.call-selected');
    Route::post('/skip', 'skip')->name('staff.skip');
    Route::get('/dashboard-data', 'data')->name('staff.data');
    Route::get('/queue-table', 'queueTable')->name('staff.queue-table');
});

// Admin Routes
//
// `auth` alone only proved *someone* was logged in — nothing checked that
// they were actually an admin, so a staff account could reach every one of
// these by just knowing the URL. `user_type:admin` closes that.
Route::prefix('admin')->middleware(['auth', 'verified', 'user_type:admin'])->group(function () {
    // The old /admin welcome page only linked to pages the sidebar already
    // reaches; /admin now just lands on the real dashboard.
    Route::get('/', fn () => redirect()->route('dashboard.with_type', ['user_type' => 'admin']))->name('admin');

    // Only index/destroy exist on the controller. These used to be a
    // Route::resource(), which also registered create/show/edit/update
    // routes pointing at nothing — every one of them 500'd with "method
    // does not exist".
    Route::get('/queues', [AdminQueueController::class, 'index'])->name('admin.queues');
    Route::delete('/queues/{id}', [AdminQueueController::class, 'destroy'])->whereNumber('id')->name('queues.destroy');

    Route::delete('/delete-old', [AdminQueueController::class, 'deleteOld'])->name('queues.deleteOld');
    Route::get('/queues-archive', [AdminQueueController::class, 'archived'])->name('queues.archived');
    Route::post('/queues/{id}/restore', [AdminQueueController::class, 'restore'])->whereNumber('id')->name('queues.restore');
    Route::delete('/queues/{id}/purge', [AdminQueueController::class, 'purge'])->whereNumber('id')->name('queues.purge');

    Route::controller(AdminStaffController::class)->group(function () {
        Route::get('/staff', 'index')->name('admin.staff');
        Route::post('/staff', 'store')->name('staff.store');
        Route::put('/staff/{id}', 'update')->whereNumber('id')->name('staff.update');
        Route::delete('/staff/{id}', 'destroy')->whereNumber('id')->name('staff.destroy');
    });

    Route::controller(AdminServiceController::class)->group(function () {
        Route::get('/services', 'index')->name('admin.services');
        Route::post('/services', 'store')->name('services.store');
        Route::put('/services/{id}', 'update')->whereNumber('id')->name('services.update');
        Route::delete('/services/{id}', 'destroy')->whereNumber('id')->name('services.destroy');
    });
});

// Kiosk
//
// The one public write endpoint, so it's rate-limited: a visitor needs a
// handful of tickets a minute at most, and without a cap a script could
// flood a department's queue (and the broadcast channel) with fake ones.
Route::prefix('kiosk')->controller(ClientQueueController::class)->group(function () {
    Route::get('/', 'index')->name('kiosk');
    Route::post('/', 'store')->middleware('throttle:20,1')->name('kiosk.store');
    Route::get('/ticket', 'show')->name('kiosk.ticket');
});

require __DIR__.'/auth.php';
