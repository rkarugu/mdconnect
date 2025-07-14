<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Facility\WalletController;

/*
|--------------------------------------------------------------------------
| Facility Wallet Routes
|--------------------------------------------------------------------------
|
| All routes related to facility wallet management
|
*/

Route::prefix('facility/wallet')->name('facility.wallet.')->middleware(['auth', 'verified', 'role:facility-admin'])->group(function () {
    // Wallet Dashboard
    Route::get('/', [WalletController::class, 'show'])->name('show');
    
    // Transactions
    Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/export', [WalletController::class, 'exportTransactions'])->name('transactions.export');
    
    // Payouts
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [WalletController::class, 'payouts'])->name('index');
        Route::get('/request', [WalletController::class, 'requestPayout'])->name('request');
        Route::post('/request', [WalletController::class, 'processPayoutRequest'])->name('process');
        Route::get('/{payout}', [WalletController::class, 'showPayout'])->name('show');
        Route::post('/{payout}/cancel', [WalletController::class, 'cancelPayout'])->name('cancel');
    });
    
    // Alias for backward compatibility
    Route::get('/payouts', [WalletController::class, 'payouts'])->name('payouts');
    
    // Top-up
    Route::prefix('top-up')->name('top-up.')->group(function () {
        Route::get('/', [WalletController::class, 'showTopUpForm'])->name('form');
        Route::post('/process', [WalletController::class, 'processTopUp'])->name('process');
        Route::get('/success/{transaction}', [WalletController::class, 'topUpSuccess'])->name('success');
        Route::get('/cancel', [WalletController::class, 'topUpCancelled'])->name('cancel');
    });
    
    // Webhooks for payment processing
    Route::post('/webhook/mpesa', [WalletController::class, 'handleMpesaWebhook'])->name('webhook.mpesa');
    Route::post('/webhook/stripe', [WalletController::class, 'handleStripeWebhook'])->name('webhook.stripe');
});
