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
use App\Http\Controllers\Web\Facility\LocumShiftController as WebLocumShiftController;
use App\Http\Controllers\Web\Facility\ShiftApplicationController as WebShiftApplicationController;
use App\Models\MedicalWorker; // Added for password reset
use Illuminate\Support\Facades\Hash; // Added for password reset
use App\Http\Controllers\Web\Facility\DashboardController as FacilityDashboardController;
use App\Http\Controllers\Api\MedicalWorkerAuthController;
use App\Http\Controllers\Api\MedicalWorkerDashboardController;
use App\Http\Controllers\Api\Worker\LocumShiftController as ApiWorkerLocumShiftController;


// Temporary route to reset Ayden's password
Route::get('/admin/temp-reset-ayden-password', function () {
    if (!auth()->check() || !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
        return response('Unauthorized. You must be an authenticated Admin.', 403);
    }

    $emailToReset = 'ayden@uptownnvintage.com';
    $newPassword = 'P@ssword123';

    $worker = App\Models\MedicalWorker::where('email', $emailToReset)->first();

    if (!$worker) {
        return "Medical worker with email {$emailToReset} not found.";
    }

    try {
        $worker->password = Illuminate\Support\Facades\Hash::make($newPassword);
        $worker->save();
        return "Password for {$emailToReset} has been reset successfully to '{$newPassword}'. Please remove this route now.";
    } catch (\Exception $e) {
        return "Error resetting password for {$emailToReset}: " . $e->getMessage();
    }
})->middleware(['web', 'auth', 'verified'])->name('temp.reset_password');

/*
|--------------------------------------------------------------------------
| Admin Routes (Authenticated & Prefixed with /admin)
|--------------------------------------------------------------------------
*/

// Main Dashboard Route - Unprefixed name but prefixed URL
Route::prefix('admin')->middleware(['web', 'auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Admin routes without prefix
Route::prefix('admin')->middleware(['web', 'auth', 'verified'])->group(function () {
    // Remove any name prefix for this specific group
    Route::name('')->group(function () {
        // System Settings
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings');

        // Profile & Change Password
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/change-password', [AdminProfileController::class, 'showChangePasswordForm'])->name('admin.change-password');
        Route::post('/change-password', [AdminProfileController::class, 'changePassword'])->name('admin.change-password.update');

        // Admin-only resource routes
        Route::middleware('admin')->group(function () {
            Route::resource('roles', RoleController::class)->middleware('role:super-admin');
            Route::resource('activity-logs', ActivityLogController::class);
        });
    });
});

/*
|--------------------------------------------------------------------------
| External Medical Worker Registration Routes (Public Access)
|--------------------------------------------------------------------------
*/

// These routes must be outside any authentication middleware to be publicly accessible
Route::middleware(['web'])->group(function () {
    Route::get('/admin/medical_workers/register', [MedicalWorkerController::class, 'create'])
        ->name('medical_workers.register');
    Route::post('/admin/medical_workers/register', [MedicalWorkerController::class, 'store'])
        ->name('medical_workers.store');
});

// Diagnostic form for troubleshooting form submission
Route::middleware(['web'])->get('/admin/medical_workers/debug-form', function() {
    return view('admin.medical_workers.form_debug');
})->name('medical_workers.debug_form');
// Route removed - using main store route for debugging

/*
|--------------------------------------------------------------------------
| Medical Worker Routes (Without Admin Prefix in Route Names)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['web', 'auth', 'verified'])->group(function () {
    // Remove any name prefix for this specific group
    Route::name('')->group(function () {
        // Admin route to create new workers internally
        Route::get('medical_workers/create', [MedicalWorkerController::class, 'create'])
            ->name('medical_workers.create')
            ->middleware('admin'); // Add admin middleware to restrict access
            
        // Medical worker routes (admin access only)
        Route::middleware('admin')->group(function () {
            // Verification stage - must come BEFORE resource route
            Route::get('medical_workers/verification', [MedicalWorkerController::class, 'verification'])
                ->name('medical_workers.verification');
            
            // Approval stage - must come BEFORE resource route
            Route::get('medical_workers/approval', [MedicalWorkerController::class, 'approval'])
                ->name('medical_workers.approval');
            
            // Individual medical worker actions
            Route::put('medical_workers/{medical_worker}/verify', [MedicalWorkerController::class, 'verify'])
                ->name('medical_workers.verify');
            Route::put('medical_workers/{medical_worker}/approve', [MedicalWorkerController::class, 'approve'])
                ->name('medical_workers.approve');
            Route::put('medical_workers/{medical_worker}/reject', [MedicalWorkerController::class, 'reject'])
                ->name('medical_workers.reject');
                
            // Resource route - must come AFTER specific routes to avoid conflicts
            Route::resource('medical_workers', MedicalWorkerController::class)->except(['create', 'store'])
                ->names([
                    'index' => 'medical_workers.index',
                    'show' => 'medical_workers.show',
                    'edit' => 'medical_workers.edit',
                    'update' => 'medical_workers.update',
                    'destroy' => 'medical_workers.destroy',
                ]);
        });
        
        // Document management and status updates
        Route::get('medical_workers/{medical_worker}/documents/{document}/preview', [MedicalWorkerController::class, 'previewDocument'])
            ->name('medical_workers.preview_document');
        Route::put('medical_workers/{medical_worker}/documents/{document}/verify', [MedicalWorkerController::class, 'verifyDocument'])
            ->name('medical_workers.verify_document');
        Route::put('medical_workers/{medical_worker}/status', [MedicalWorkerController::class, 'updateStatus'])
            ->name('medical_workers.update_status');
    });
});

/*
|--------------------------------------------------------------------------
| User Management Routes (Without Admin Prefix in Route Names)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['web', 'auth', 'verified'])->group(function () {
    // Remove any name prefix for this specific group
    Route::name('')->group(function () {
        Route::middleware('admin')->group(function () {
            // Resource route
            Route::resource('users', UserController::class)
                ->names([
                    'index' => 'users.index',
                    'create' => 'users.create',
                    'store' => 'users.store',
                    'show' => 'users.show',
                    'edit' => 'users.edit',
                    'update' => 'users.update',
                    'destroy' => 'users.destroy',
                ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Medical Specialties Routes (Without Admin Prefix in Route Names)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['web', 'auth', 'verified'])->group(function () {
    // Remove any name prefix for this specific group
    Route::name('')->group(function () {
        Route::middleware('admin')->group(function () {
            // Resource route for medical specialties
            Route::resource('medical_specialties', MedicalSpecialtyController::class)
                ->names([
                    'index' => 'medical_specialties.index',
                    'create' => 'medical_specialties.create',
                    'store' => 'medical_specialties.store',
                    'show' => 'medical_specialties.show',
                    'edit' => 'medical_specialties.edit',
                    'update' => 'medical_specialties.update',
                    'destroy' => 'medical_specialties.destroy',
                ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Medical Facilities Routes (Without Admin Prefix in Route Names)
|--------------------------------------------------------------------------
*/
// This is what the group should look like
Route::middleware(['auth', 'role:facility-admin'])->prefix('facility')->name('facility.')->group(function () {
    Route::get('dashboard', [FacilityDashboardController::class, 'index'])->name('dashboard'); // Add this line
    Route::resource('locum-shifts', WebLocumShiftController::class);
    Route::post('locum-shifts/{locum_shift}/applications/{medical_worker}/accept', [WebShiftApplicationController::class, 'accept'])->name('locum-shifts.applications.accept');
});
Route::prefix('admin')->middleware(['web', 'auth', 'verified'])->group(function () {
    // Remove any name prefix for this specific group
    Route::name('')->group(function () {
        Route::middleware('admin')->group(function () {
            // Specific routes first to avoid conflicts with resource routes
            Route::get('medical_facilities/verification', [AdminMedicalFacilityController::class, 'verification'])
                ->name('medical_facilities.verification');
                
            // Resource route
            Route::resource('medical_facilities', AdminMedicalFacilityController::class)
                ->names([
                    'index' => 'medical_facilities.index',
                    'create' => 'medical_facilities.create',
                    'store' => 'medical_facilities.store',
                    'show' => 'medical_facilities.show',
                    'edit' => 'medical_facilities.edit',
                    'update' => 'medical_facilities.update',
                    'destroy' => 'medical_facilities.destroy',
                ]);
        });
        
        // Medical Facility Status Management
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
        Route::post('medical_facilities/{medical_facility}/activate', [AdminMedicalFacilityController::class, 'activate'])
            ->name('medical_facilities.activate');
        
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
        Route::post('medical_facilities/{medical_facility}/documents/upload', [AdminMedicalFacilityController::class, 'uploadDocument'])
            ->name('medical_facilities.documents.store');
        Route::delete('medical_facilities/{medical_facility}/documents/{document}/delete', [AdminMedicalFacilityController::class, 'deleteDocument'])
            ->name('medical_facilities.documents.delete');
    });
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

// Redirect non-admin dashboard to admin dashboard
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return redirect('/admin/dashboard');
});

// Email Testing Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/test', [\App\Http\Controllers\EmailTestController::class, 'showTestForm'])->name('email.test');
    Route::post('/email/test/send', [\App\Http\Controllers\EmailTestController::class, 'sendTestEmail'])->name('email.test.send');
});

/*
|--------------------------------------------------------------------------
| Medical Facilities Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('medical-facilities')->name('public_medical_facilities.')->group(function () {
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
| Medical Worker Public Routes
|--------------------------------------------------------------------------
*/
// Public routes for medical workers to view and check their application status
Route::get('/medical-workers/status', [\App\Http\Controllers\MedicalWorkerController::class, 'showStatus'])->name('medical-workers.status');
Route::post('/medical-workers/status/lookup', [\App\Http\Controllers\MedicalWorkerController::class, 'lookupStatus'])->name('medical-workers.lookup-status');

// Public route for medical worker registration
Route::get('/medical-workers/register', [\App\Http\Controllers\MedicalWorkerController::class, 'showRegistrationForm'])
    ->name('medical-workers.register');
Route::post('/medical-workers/register', [\App\Http\Controllers\MedicalWorkerController::class, 'register'])
    ->name('medical-workers.register.submit');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/




require __DIR__.'/auth.php';
