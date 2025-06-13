<?php

use App\Http\Controllers\Api\MedicalWorkerAuthController;
use Illuminate\Support\Facades\Route;

// Test route to verify routing is working
Route::get('/test', function() {
    return response()->json(['message' => 'Test route is working!']);
});

/*
|--------------------------------------------------------------------------
| Medical Worker API Routes
|--------------------------------------------------------------------------
|
| These routes are specific to the medical worker mobile application
|
*/

// Public routes
Route::post('/medical-worker/register', [MedicalWorkerAuthController::class, 'register']);
Route::post('/medical-worker/login', [MedicalWorkerAuthController::class, 'login']);

// Protected routes
Route::middleware('auth:medical-worker')->group(function () {
    // Auth routes
    Route::get('/medical-worker/me', [MedicalWorkerAuthController::class, 'me']);
    Route::post('/medical-worker/logout', [MedicalWorkerAuthController::class, 'logout']);
    Route::put('/medical-worker/profile', [MedicalWorkerAuthController::class, 'updateProfile']);
    Route::post('/medical-worker/change-password', [MedicalWorkerAuthController::class, 'changePassword']);
    
    // Add other medical worker specific routes here...
});
