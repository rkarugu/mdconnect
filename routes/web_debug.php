<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\MedicalFacility;
use App\Models\Role;

Route::get('/debug/user-facility', function () {
    // Check if user is authenticated
    if (!auth()->check()) {
        return response()->json([
            'authenticated' => false,
            'message' => 'User is not authenticated',
            'session' => session()->all()
        ]);
    }

    $user = auth()->user();
    
    // Get user details
    $userData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role_id' => $user->role_id,
        'has_facility_relationship' => method_exists($user, 'facility'),
    ];

    // Try to get facility
    try {
        $facility = $user->facility;
        $userData['facility'] = $facility ? [
            'id' => $facility->id,
            'facility_name' => $facility->facility_name,
            'status' => $facility->status,
        ] : null;
    } catch (\Exception $e) {
        $userData['facility_error'] = $e->getMessage();
    }

    // Check if user has facility-admin role
    $userData['is_facility_admin'] = $user->hasRole('facility-admin');

    // Check database directly
    $dbFacility = MedicalFacility::where('user_id', $user->id)->first();
    $userData['direct_db_facility'] = $dbFacility ? [
        'id' => $dbFacility->id,
        'facility_name' => $dbFacility->facility_name,
        'user_id' => $dbFacility->user_id,
    ] : null;

    return response()->json([
        'authenticated' => true,
        'user' => $userData,
        'session' => [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
        ],
    ]);
})->middleware(['web', 'auth', 'verified']);

// List all users and their roles
Route::get('/debug/users', function () {
    $users = User::with('role')->get();
    
    $result = [];
    foreach ($users as $user) {
        $result[] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ? $user->role->name : 'None',
            'has_facility' => $user->facility ? 'Yes' : 'No',
            'facility_id' => $user->facility ? $user->facility->id : null,
        ];
    }
    
    return response()->json($result);
})->middleware(['web', 'auth']);

// Create wallet for facility
Route::get('/debug/create-wallet', function () {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    if (!$user->facility) {
        return response()->json(['error' => 'User is not associated with a facility'], 400);
    }
    
    $facility = $user->facility;
    
    try {
        // Check if wallet already exists
        if ($facility->wallet) {
            return response()->json([
                'message' => 'Wallet already exists for this facility',
                'wallet' => $facility->wallet
            ]);
        }
        
        // Create new wallet using the correct model and fields
        $wallet = \App\Models\FacilityWallet::create([
            'medical_facility_id' => $facility->id,
            'balance' => 0,
            'status' => 'active',
        ]);
        
        // Refresh the facility to load the new wallet
        $facility->load('wallet');
        
        return response()->json([
            'message' => 'Wallet created successfully',
            'wallet' => $wallet,
            'facility_wallet' => $facility->wallet
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to create wallet',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware(['web', 'auth', 'verified', 'role:facility-admin']);

// Debug wallet access
Route::get('/debug/wallet-access', function () {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    // Check facility association
    $facility = $user->facility;
    $hasFacility = (bool)$facility;
    
    // Check wallet
    $wallet = $hasFacility ? $facility->wallet : null;
    $hasWallet = (bool)$wallet;
    
    // Check middleware
    $middleware = app('router')->getCurrentRoute()->middleware();
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ? $user->role->name : 'None',
        ],
        'facility' => $hasFacility ? [
            'id' => $facility->id,
            'name' => $facility->facility_name,
            'status' => $facility->status,
        ] : null,
        'wallet' => $hasWallet ? [
            'id' => $wallet->id,
            'balance' => $wallet->balance,
            'status' => $wallet->status,
        ] : null,
        'access' => [
            'has_facility' => $hasFacility,
            'has_wallet' => $hasWallet,
            'middleware' => $middleware,
        ],
        'session' => [
            'session_id' => session()->getId(),
            'all' => session()->all(),
        ]
    ]);
})->middleware(['web', 'auth', 'verified', 'role:facility-admin']);
