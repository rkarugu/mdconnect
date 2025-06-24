<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MedicalWorkerAuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MedicalWorkerDashboardController;

// Public routes
Route::post('medical-worker/register', [MedicalWorkerAuthController::class, 'register']);
Route::post('medical-worker/login', [MedicalWorkerAuthController::class, 'login']);

// Protected routes
Route::middleware('auth:medical-worker')->group(function () {
    Route::get('medical-worker/me', [MedicalWorkerAuthController::class, 'me']);
    Route::post('medical-worker/logout', [MedicalWorkerAuthController::class, 'logout']);
    Route::put('medical-worker/profile', [MedicalWorkerAuthController::class, 'updateProfile']);
    Route::post('medical-worker/change-password', [MedicalWorkerAuthController::class, 'changePassword']);
    Route::get('jobs', [JobController::class, 'index']);

    // Dashboard routes
    Route::get('worker/dashboard', [MedicalWorkerDashboardController::class, 'index']);
    Route::get('worker/shifts/upcoming', [MedicalWorkerDashboardController::class, 'upcomingShifts']);
    Route::get('worker/shifts/instant-requests', [MedicalWorkerDashboardController::class, 'instantRequests']);
    Route::get('worker/shifts/bid-invitations', [MedicalWorkerDashboardController::class, 'bidInvitations']);
    Route::get('worker/shifts/history', [MedicalWorkerDashboardController::class, 'shiftHistory']);

    // Action routes
    Route::post('worker/shifts/instant-requests/{id}/accept', [MedicalWorkerDashboardController::class, 'acceptInstantRequest']);
    Route::post('worker/shifts/bid-invitations/{id}/apply', [MedicalWorkerDashboardController::class, 'applyToBidInvitation']);
});
