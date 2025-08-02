<?php

use App\Http\Controllers\Api\MedicalWorkerAuthController;
use App\Http\Controllers\Api\MedicalWorkerDashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Worker\LocumShiftController;
use App\Http\Controllers\Api\Worker\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Medical Worker API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for medical workers.
|
*/

// Auth & Profile
Route::get('/me', [MedicalWorkerAuthController::class, 'me'])->name('me');
Route::post('/logout', [MedicalWorkerAuthController::class, 'logout'])->name('logout');
Route::put('/profile', [MedicalWorkerDashboardController::class, 'updateProfile'])->name('profile.update');
Route::post('/change-password', [MedicalWorkerDashboardController::class, 'changePassword'])->name('change-password');

// Dashboard
Route::get('/dashboard', [MedicalWorkerDashboardController::class, 'index'])->name('dashboard');

// Shifts & Applications
Route::prefix('locum-shifts')->name('locum-shifts.')->group(function () {
    Route::get('/available', [LocumShiftController::class, 'availableShifts'])->name('available');
    Route::post('/{locum_shift}/apply', [LocumShiftController::class, 'apply'])->name('apply');
    Route::get('/my-applications', [LocumShiftController::class, 'myApplications'])->name('my-applications');
    Route::post('/{id}/start', [LocumShiftController::class, 'startShift'])->name('start');
});

// Wallet
Route::prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('index');
    Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
});

// Notifications Routes (Cleaned up and Standardized)
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});