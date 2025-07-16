<?php

use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('kiosk.index');
})->name('home');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', function () {
    return view('kiosk.index');
})->name('home');

// Kiosk Routes
Route::prefix('kiosk')->group(function () {
    Route::get('/', function () {
        return view('kiosk.index');
    })->name('kiosk');
    
    Route::get('/ticket', function () {
        return view('kiosk.ticket');
    })->name('kiosk.ticket');
});

// Display Routes
Route::prefix('display')->group(function () {
    Route::get('/', function () {
        return view('display.counter');
    })->name('display');
});

// Staff Routes
Route::prefix('staff')->group(function () {
    Route::get('/', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');
    
    Route::get('/call', function () {
        return view('staff.call');
    })->name('staff.call');
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

    // Crud
    Route::controller(AdminStaffController::class)->group(function(){
        Route::get('/staff', action: 'index')->name('admin.staff');
        Route::post('/store', 'store')->name('staff.store');
        Route::post('/udpate/{id}', 'update')->name('staff.update');
    });
});



require __DIR__.'/auth.php';
