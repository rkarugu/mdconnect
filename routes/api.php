<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\Facility\DashboardController;
use App\Http\Controllers\Api\Facility\LocumShiftController;
use App\Http\Controllers\Api\Facility\ShiftApplicationController;
use App\Http\Controllers\Api\MedicalWorkerAuthController;
use App\Http\Controllers\Api\MedicalWorkerDashboardController;
use App\Http\Controllers\Api\Worker\LocumShiftController as ApiWorkerLocumShiftController;

// Public routes for Medical Worker SPA
Route::post('medical-worker/login', [MedicalWorkerAuthController::class, 'login']);
Route::post('medical-worker/register', [MedicalWorkerAuthController::class, 'register']);

// Protected routes for Medical Worker SPA
Route::middleware('auth:sanctum')->prefix('worker')->name('worker.')->group(function () {
    Route::post('logout', [MedicalWorkerAuthController::class, 'logout'])->name('logout');
    Route::get('me', [MedicalWorkerAuthController::class, 'me'])->name('me');
    Route::put('profile', [MedicalWorkerAuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('change-password', [MedicalWorkerAuthController::class, 'changePassword'])->name('change-password');

    // Dashboard routes
    Route::get('dashboard', [MedicalWorkerDashboardController::class, 'index'])->name('dashboard');
    Route::get('shifts/upcoming', [MedicalWorkerDashboardController::class, 'upcomingShifts'])->name('shifts.upcoming');
    Route::get('shifts/instant-requests', [MedicalWorkerDashboardController::class, 'instantRequests'])->name('shifts.instant-requests');
    Route::get('shifts/bid-invitations', [MedicalWorkerDashboardController::class, 'bidInvitations'])->name('shifts.bid-invitations');
    Route::get('shifts/history', [MedicalWorkerDashboardController::class, 'shiftHistory'])->name('shifts.history');

    // Action routes
    Route::post('shifts/instant-requests/{id}/accept', [MedicalWorkerDashboardController::class, 'acceptInstantRequest'])->name('shifts.instant-requests.accept');
    Route::post('shifts/bid-invitations/{id}/apply', [MedicalWorkerDashboardController::class, 'applyToBidInvitation'])->name('shifts.bid-invitations.apply');

    // Wallet routes
    Route::get('wallet', [\App\Http\Controllers\Api\Worker\WalletController::class, 'show'])->name('wallet');
    Route::get('wallet/transactions', [\App\Http\Controllers\Api\Worker\WalletController::class, 'transactions'])->name('wallet.transactions');

    // Locum Shifts for Workers
    Route::get('locum-shifts/available', [ApiWorkerLocumShiftController::class, 'availableShifts'])->name('locum-shifts.available');
    Route::post('locum-shifts/{locum_shift}/apply', [ApiWorkerLocumShiftController::class, 'apply'])->name('locum-shifts.apply');
        Route::post('locum-shifts/{id}/start', [ApiWorkerLocumShiftController::class, 'start'])->name('locum-shifts.start');
    Route::get('my-locum-applications', [ApiWorkerLocumShiftController::class, 'myApplications'])->name('locum-shifts.my-applications');
});

// Facility Admin API routes
Route::middleware(['auth:sanctum', 'role:facility-admin'])->prefix('facility')->name('api.facility.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('locum-shifts', LocumShiftController::class);
    Route::get('locum-shifts/{locum_shift}/applicants', [ShiftApplicationController::class, 'index'])->name('locum-shifts.applicants.index');
    Route::post('locum-shifts/{locum_shift}/accept/{medical_worker}', [ShiftApplicationController::class, 'accept'])->name('locum-shifts.applicants.accept');

    // Wallet routes for Facility
    Route::get('wallet', [\App\Http\Controllers\Api\Facility\FacilityWalletController::class, 'show'])->name('wallet');
    Route::get('wallet/transactions', [\App\Http\Controllers\Api\Facility\FacilityWalletController::class, 'transactions'])->name('wallet.transactions');
    Route::post('wallet/top-up', [\App\Http\Controllers\Api\Facility\FacilityWalletController::class, 'topUp'])->name('wallet.top-up');
});
