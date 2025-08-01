<?php

use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\ClientQueueController;
use App\Http\Controllers\DisplayAllQueueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('kiosk.index');
// })->name('home');

// Dashboard
Route::get('/dashboard', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Display Routes
Route::prefix('display')->group(function () {
    Route::get('/', [DisplayAllQueueController::class, 'index'])->name('display');
    Route::get('/serving-patients', [DisplayAllQueueController::class, 'servingPatients']);
});

// Staff Routes
Route::prefix('staff')->group(function () {
    Route::get('/', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');
    
    Route::post('/call-next', [StaffController::class, 'callNext'])->name('staff.call-next');
    Route::post('/call-previous', [StaffController::class, 'callPrevious']);
    Route::post('/call', [StaffController::class, 'call']);
    Route::get('/dashboard-data', [StaffController::class, 'data']);
});


// Admin Routes
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('admin');

    Route::get('/queues', function () {
        return view('admin.queues');
    })->name('admin.queues');
    
    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('admin.analytics');

     // CRUD
    Route::controller(App\Http\Controllers\AdminStaffController::class)->group(function () {
        Route::get('/staff', 'index')->name('admin.staff');
        Route::post('/store', 'store')->name('staff.store');
        Route::put('/staff/update/{id}', 'update')->name('staff.update');
        Route::delete('/staff/destroy/{id}', 'destroy')->name('staff.destroy'); 
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
