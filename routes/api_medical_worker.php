<?php

use App\Http\Controllers\Api\MedicalWorkerAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Medical Worker API Routes
|--------------------------------------------------------------------------
|
| These routes are specific to the medical worker mobile application
|
*/

// Public routes
Route::post('register', [MedicalWorkerAuthController::class, 'register']);
Route::post('login', [MedicalWorkerAuthController::class, 'login']);

// Protected routes
Route::middleware('auth:medical-worker')->group(function () {
    // Auth routes
    Route::get('me', [MedicalWorkerAuthController::class, 'me']);
    Route::post('logout', [MedicalWorkerAuthController::class, 'logout']);
    Route::put('profile', [MedicalWorkerAuthController::class, 'updateProfile']);
    Route::post('change-password', [MedicalWorkerAuthController::class, 'changePassword']);
    
    // Add other medical worker specific routes here...
});
