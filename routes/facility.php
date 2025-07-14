<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Facility\WalletController;

/*
|--------------------------------------------------------------------------
| Facility Wallet Routes
|--------------------------------------------------------------------------
*/

Route::prefix('facility')->middleware(['auth', 'verified', 'role:facility-admin'])->name('facility.')->group(function () {
    // Wallet Routes
    Route::prefix('wallet')->name('wallet.')->group(function () {
        // Show wallet dashboard
        Route::get('/', [WalletController::class, 'show'])->name('show');
        
        // Show wallet transactions
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        
        // Payout management
        Route::get('/payouts', [WalletController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/request', [WalletController::class, 'requestPayout'])->name('payouts.request');
        Route::get('/payouts/{payout}', [WalletController::class, 'showPayout'])->name('payouts.show');
        
        // Wallet top-up (if needed)
        Route::get('/top-up', [WalletController::class, 'showTopUpForm'])->name('top-up.show');
        Route::post('/top-up', [WalletController::class, 'processTopUp'])->name('top-up.process');
    });
});
