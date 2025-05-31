<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicalFacilityController;
use App\Http\Controllers\LocumJobController;
use App\Http\Controllers\FacilityJobController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\MedicalWorkerController;
use App\Http\Controllers\Admin\MedicalSpecialtyController;
use App\Http\Controllers\Admin\MedicalDocumentController;
use App\Http\Controllers\Admin\MedicalFacilityController as AdminMedicalFacilityController;

/*
|--------------------------------------------------------------------------
| Admin Routes (Authenticated & Prefixed with /admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // System Settings
    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings');

    // Profile & Change Password
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::get('/change-password', [ChangePasswordController::class, 'index'])->name('change-password');

    // Admin-only resource routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('activity-logs', ActivityLogController::class);
    });

    // Medical Workers and Specialties routes (accessible to all authenticated users)
    Route::resource('medical_workers', MedicalWorkerController::class);
    
    // Medical Facilities Admin Routes
    Route::resource('medical_facilities', AdminMedicalFacilityController::class);
    
    // Medical Facility Verification and Status Management
    Route::get('medical_facilities/verification', [AdminMedicalFacilityController::class, 'verification'])
        ->name('medical_facilities.verification');
    Route::post('medical_facilities/{medical_facility}/verify_status', [AdminMedicalFacilityController::class, 'updateStatus'])
        ->name('medical_facilities.verify_status');
    Route::post('medical_facilities/{medical_facility}/verify', [AdminMedicalFacilityController::class, 'verify'])
        ->name('medical_facilities.verify');
    Route::post('medical_facilities/{medical_facility}/approve', [AdminMedicalFacilityController::class, 'approve'])
        ->name('medical_facilities.approve');
    Route::post('medical_facilities/{medical_facility}/reject', [AdminMedicalFacilityController::class, 'reject'])
        ->name('medical_facilities.reject');
    Route::post('medical_facilities/{medical_facility}/suspend', [AdminMedicalFacilityController::class, 'suspend'])
        ->name('medical_facilities.suspend');
    Route::post('medical_facilities/{medical_facility}/reinstate', [AdminMedicalFacilityController::class, 'reinstate'])
        ->name('medical_facilities.reinstate');
    
    // Medical Facility Document Management
    Route::get('medical_facilities/{medical_facility}/documents', [AdminMedicalFacilityController::class, 'documents'])
        ->name('medical_facilities.documents');
    Route::post('medical_facilities/{medical_facility}/documents/verify_all', [AdminMedicalFacilityController::class, 'verifyAllDocuments'])
        ->name('medical_facilities.documents.verify_all');
    Route::get('medical_facilities/{medical_facility}/documents/{document}/preview', [AdminMedicalFacilityController::class, 'previewDocument'])
        ->name('medical_facilities.documents.preview');
    Route::post('medical_facilities/{medical_facility}/documents/{document}/verify', [AdminMedicalFacilityController::class, 'verifyDocument'])
        ->name('medical_facilities.documents.verify');
    Route::delete('medical_facilities/{medical_facility}/documents/{document}', [AdminMedicalFacilityController::class, 'destroyDocument'])
        ->name('medical_facilities.documents.destroy');
        
    // Document Upload Management
    Route::get('medical_facilities/{medical_facility}/documents/upload', [AdminMedicalFacilityController::class, 'showDocumentUploadForm'])
        ->name('medical_facilities.documents.upload');
    Route::post('medical_facilities/{medical_facility}/documents/upload', [AdminMedicalFacilityController::class, 'uploadDocuments'])
        ->name('medical_facilities.documents.upload.store');
    Route::delete('medical_facilities/{medical_facility}/documents/{document}/delete', [AdminMedicalFacilityController::class, 'deleteDocument'])
        ->name('medical_facilities.documents.delete');
    
    // Step 1: Initial registration
    Route::get('medical_workers/register', [MedicalWorkerController::class, 'create'])
        ->name('medical_workers.register');
    Route::post('medical_workers/register', [MedicalWorkerController::class, 'store'])
        ->name('medical_workers.store');
    
    // Step 2: Document verification (Super Admin only)
    Route::middleware('admin')->group(function () {
        // Verification stage
        Route::get('medical_workers/verification', [MedicalWorkerController::class, 'verification'])
            ->name('medical_workers.verification');
        Route::put('medical_workers/{medical_worker}/verify', [MedicalWorkerController::class, 'verify'])
            ->name('medical_workers.verify');
        
        // Approval stage
        Route::get('medical_workers/approval', [MedicalWorkerController::class, 'approval'])
            ->name('medical_workers.approval');
        Route::put('medical_workers/{medical_worker}/approve', [MedicalWorkerController::class, 'approve'])
            ->name('medical_workers.approve');
        Route::put('medical_workers/{medical_worker}/reject', [MedicalWorkerController::class, 'reject'])
            ->name('medical_workers.reject');
    });

    // Document management and status updates
    Route::get('medical_workers/{medical_worker}/documents/{document}/preview', [MedicalWorkerController::class, 'previewDocument'])
        ->name('medical_workers.preview_document');
    Route::put('medical_workers/{medical_worker}/documents/{document}/verify', [MedicalWorkerController::class, 'verifyDocument'])
        ->name('medical_workers.verify_document');
    Route::put('medical_workers/{medical_worker}/status', [MedicalWorkerController::class, 'updateStatus'])
        ->name('medical_workers.update_status');
    
    Route::resource('medical_specialties', MedicalSpecialtyController::class);
});

/*
|--------------------------------------------------------------------------
| Facility Job Posting Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Facility Job Posting Routes
    Route::resource('facility_jobs', FacilityJobController::class);
    Route::put('facility_jobs/{job}/update-status', [FacilityJobController::class, 'updateStatus'])
        ->name('facility_jobs.update-status');
    Route::put('facility_jobs/{job}/applications/{application}', [FacilityJobController::class, 'processApplication'])
        ->name('facility_jobs.process-application');
});

/*
|--------------------------------------------------------------------------
| Redirect Root & /dashboard to Admin Dashboard
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/admin/dashboard');

Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Medical Facilities Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('medical-facilities')->name('medical_facilities.')->group(function () {
    Route::get('/', [MedicalFacilityController::class, 'index'])->name('index');
    Route::get('/register', [MedicalFacilityController::class, 'create'])->name('create');
    Route::post('/register', [MedicalFacilityController::class, 'store'])->name('store');
    
    // Authenticated routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/{facility}', [MedicalFacilityController::class, 'show'])->name('show');
        Route::get('/{facility}/edit', [MedicalFacilityController::class, 'edit'])->name('edit');
        Route::put('/{facility}', [MedicalFacilityController::class, 'update'])->name('update');
        Route::delete('/{facility}', [MedicalFacilityController::class, 'destroy'])->name('destroy');
        
        // Document management
        Route::get('/{facility}/documents/{document}/preview', [MedicalFacilityController::class, 'previewDocument'])
            ->name('documents.preview');
    });
});

/*
|--------------------------------------------------------------------------
| Locum Job Routes
|--------------------------------------------------------------------------
*/
Route::prefix('locum-jobs')->name('locum_jobs.')->middleware(['auth'])->group(function () {
    Route::get('/', [LocumJobController::class, 'index'])->name('index');
    Route::get('/create', [LocumJobController::class, 'create'])->name('create');
    Route::post('/', [LocumJobController::class, 'store'])->name('store');
    Route::get('/{job}', [LocumJobController::class, 'show'])->name('show');
    Route::get('/{job}/edit', [LocumJobController::class, 'edit'])->name('edit');
    Route::put('/{job}', [LocumJobController::class, 'update'])->name('update');
    Route::delete('/{job}', [LocumJobController::class, 'destroy'])->name('destroy');
    
    // Job applications
    Route::post('/{job}/apply', [LocumJobController::class, 'apply'])->name('apply');
    Route::get('/{job}/applications', [LocumJobController::class, 'applications'])->name('applications');
    Route::put('/{job}/applications/{application}', [LocumJobController::class, 'updateApplication'])->name('applications.update');
});

/*
|--------------------------------------------------------------------------
| General Authenticated User Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
