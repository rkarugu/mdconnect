<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MedicalWorkerAuthController;
use App\Http\Controllers\Api\JobController;

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
});
