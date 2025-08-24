<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User\TimeTrackerController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TimeTrackerController as AdminTimeTrackerController;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
})->name('home');

Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');


Route::get('user/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.dashboard');

// Admin User Management Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('users/{user}/time-tracker', [UserController::class, 'timeTracker'])->name('users.time-tracker');

});

// Time Tracker Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/time-tracker/clock-in', [TimeTrackerController::class, 'clockIn'])->name('user.time-tracker.clock-in');
    Route::post('/time-tracker/clock-out', [TimeTrackerController::class, 'clockOut'])->name('user.time-tracker.clock-out');
    Route::resource('user/time-tracker', TimeTrackerController::class)->names('user.time-tracker');
});

require __DIR__.'/auth.php';
