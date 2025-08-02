<?php

use App\Http\Controllers\Admin\PatientManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Patient Management Routes
|--------------------------------------------------------------------------
|
| These routes handle all patient management functionality for the admin
| dashboard including CRUD operations, analytics, and reporting.
|
*/

Route::prefix('patients')->name('admin.patients.')->group(function () {
    
    // Dashboard
    Route::get('/', [PatientManagementController::class, 'index'])
        ->name('dashboard');
    
    // Patient List & Search
    Route::get('/list', [PatientManagementController::class, 'list'])
        ->name('list');
    
    // Create Patient
    Route::get('/create', [PatientManagementController::class, 'create'])
        ->name('create');
    Route::post('/create', [PatientManagementController::class, 'store'])
        ->name('store');
    
    // View Patient
    Route::get('/{patient}', [PatientManagementController::class, 'show'])
        ->name('show');
    
    // Edit Patient
    Route::get('/{patient}/edit', [PatientManagementController::class, 'edit'])
        ->name('edit');
    Route::put('/{patient}', [PatientManagementController::class, 'update'])
        ->name('update');
    
    // Delete Patient
    Route::delete('/{patient}', [PatientManagementController::class, 'destroy'])
        ->name('destroy');
    
    // Toggle Verification
    Route::patch('/{patient}/toggle-verification', [PatientManagementController::class, 'toggleVerification'])
        ->name('toggle-verification');
    
    // Analytics
    Route::get('/analytics/dashboard', [PatientManagementController::class, 'analytics'])
        ->name('analytics');
    
    // Export
    Route::get('/export/data', [PatientManagementController::class, 'export'])
        ->name('export');
});
