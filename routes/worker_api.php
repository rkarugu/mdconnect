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

// Bid Invitations & Instant Requests (Protected by Sanctum)
Route::middleware(['auth:sanctum'])->prefix('shifts')->name('shifts.')->group(function () {
    // Bid invitation routes
    Route::get('/bid-invitations', [MedicalWorkerDashboardController::class, 'bidInvitations'])->name('bid-invitations');
    Route::post('/bid-invitations/{id}/apply', [MedicalWorkerDashboardController::class, 'applyToBidInvitation'])->name('apply-bid-invitation');

    Route::get('/instant-requests', [MedicalWorkerDashboardController::class, 'instantRequests'])->name('instant-requests');
    Route::post('/instant-requests/{id}/accept', [MedicalWorkerDashboardController::class, 'acceptInstantRequest'])->name('instant-requests.accept');
});

// Shift Applications (Protected by Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/shift-applications', [MedicalWorkerDashboardController::class, 'getShiftApplications'])->name('shift-applications');
    Route::post('/shift-applications/{id}/start', [MedicalWorkerDashboardController::class, 'startShift'])->name('start-shift');
    Route::post('/shift-applications/{id}/complete', [MedicalWorkerDashboardController::class, 'completeShift'])->name('complete-shift');
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