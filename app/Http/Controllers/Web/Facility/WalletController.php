<?php

namespace App\Http\Controllers\Web\Facility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacilityWallet;
use App\Models\FacilityWalletTransaction;
use App\Models\PayoutRequest;
use App\Models\MedicalFacility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    /**
     * Show the facility wallet dashboard
     */
    public function show()
    {
        $user = Auth::user();
        
        // Check if user has a facility
        if (!$user->facility) {
            // If user doesn't have a facility, redirect to dashboard with error
            return redirect()->route('facility.dashboard')
                ->with('error', 'You are not associated with any facility. Please contact support.');
        }
        
        $facility = $user->facility;
        $wallet = $facility->wallet;
        
        // If wallet doesn't exist, create it
        if (!$wallet) {
            try {
                $wallet = FacilityWallet::create([
                    'facility_id' => $facility->id,
                    'balance' => 0,
                    'currency' => 'KES', // Default currency
                    'status' => 'active',
                ]);
            } catch (\Exception $e) {
                // Log the error and redirect with a friendly message
                \Log::error('Failed to create wallet for facility: ' . $facility->id, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('facility.dashboard')
                    ->with('error', 'Failed to initialize wallet. Please try again or contact support.');
            }
        }
        
        // Get recent transactions
        $transactions = $wallet->transactions()
            ->latest()
            ->take(5)
            ->get();
            
        // Get pending payouts
        $pendingPayouts = $facility->payoutRequests()
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate total paid out
        $totalPaidOut = $facility->payoutRequests()
            ->where('status', 'completed')
            ->sum('amount');
            
        return view('medical_facilities.wallet.show', [
            'wallet' => $wallet,
            'facility' => $facility,
            'transactions' => $transactions,
            'pendingPayouts' => $pendingPayouts,
            'totalPaidOut' => $totalPaidOut
        ]);
    }
    
    /**
     * Show wallet transactions
     */
    public function transactions(Request $request)
    {
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        $query = $wallet->transactions()->latest();
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->paginate(20)->withQueryString();
        
        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('medical_facilities.wallet.partials.transactions_table', [
                    'transactions' => $transactions
                ])->render(),
                'hasMorePages' => $transactions->hasMorePages()
            ]);
        }
        
        return view('medical_facilities.wallet.transactions', [
            'wallet' => $wallet,
            'facility' => $facility,
            'transactions' => $transactions,
            'filters' => $request->only(['type', 'date_from', 'date_to'])
        ]);
    }
    
    /**
     * Export transactions to CSV
     */
    public function exportTransactions(Request $request)
    {
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        $transactions = $wallet->transactions()
            ->when($request->filled('type'), function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->get();
        
        $fileName = 'transactions-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Date', 'Type', 'Description', 'Amount (KES)', 'Balance', 'Reference', 'Status'
            ]);
            
            // Add data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    ucfirst($transaction->type),
                    $transaction->description,
                    number_format($transaction->amount, 2),
                    number_format($transaction->balance_after, 2),
                    $transaction->reference,
                    ucfirst($transaction->status)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Show payout requests
     */
    public function payouts(Request $request)
    {
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        $query = $facility->payoutRequests()->latest();
        
        // Apply status filter
        if ($request->filled('status') && in_array($request->status, ['pending', 'processing', 'completed', 'failed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        
        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $payouts = $query->paginate(20)->withQueryString();
        
        // Calculate stats
        $totalPaidOut = $facility->payoutRequests()
            ->where('status', 'completed')
            ->sum('amount');
            
        $pendingPayouts = $facility->payoutRequests()
            ->where('status', 'pending')
            ->sum('amount');
        
        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('medical_facilities.wallet.partials.payouts_table', [
                    'payouts' => $payouts
                ])->render(),
                'hasMorePages' => $payouts->hasMorePages()
            ]);
        }
        
        return view('medical_facilities.wallet.payouts', [
            'wallet' => $wallet,
            'facility' => $facility,
            'payouts' => $payouts,
            'totalPaidOut' => $totalPaidOut,
            'pendingPayouts' => $pendingPayouts,
            'filters' => $request->only(['status', 'date_from', 'date_to'])
        ]);
    }
    
    /**
     * Show the payout request form
     */
    public function requestPayout()
    {
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        // Get saved payment methods if any
        $savedPaymentMethods = $facility->paymentMethods()->where('is_active', true)->get();
        
        return view('medical_facilities.wallet.request_payout', [
            'wallet' => $wallet,
            'facility' => $facility,
            'savedPaymentMethods' => $savedPaymentMethods
        ]);
    }
    
    /**
     * Process payout request
     */
    public function processPayoutRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000', // Minimum KES 1000
            'payment_method' => 'required|in:mpesa,bank_transfer',
            'account_details' => 'required|string|max:255',
            'save_payment_method' => 'boolean',
            'payment_method_nickname' => 'nullable|string|max:50|required_if:save_payment_method,1',
        ]);
        
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        // Check if balance is sufficient
        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance for this payout request.');
        }
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Save payment method if requested
            if ($request->boolean('save_payment_method')) {
                $facility->paymentMethods()->updateOrCreate(
                    ['nickname' => $request->payment_method_nickname],
                    [
                        'payment_method' => $request->payment_method,
                        'account_details' => $request->account_details,
                        'is_default' => $facility->paymentMethods()->count() === 0,
                        'is_active' => true
                    ]
                );
            }
            
            // Create payout request
            $payout = PayoutRequest::create([
                'facility_id' => $facility->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'account_details' => $request->account_details,
                'status' => 'pending',
                'requested_at' => now(),
                'reference' => 'PYT-' . strtoupper(uniqid()),
            ]);
            
            // Deduct from wallet balance
            $wallet->decrement('balance', $request->amount);
            
            // Record transaction
            $wallet->transactions()->create([
                'amount' => $request->amount,
                'type' => 'payout',
                'description' => 'Payout request #' . $payout->id,
                'reference' => $payout->reference,
                'status' => 'pending',
                'balance_before' => $wallet->balance + $request->amount,
                'balance_after' => $wallet->balance,
                'metadata' => [
                    'payout_id' => $payout->id,
                    'payment_method' => $request->payment_method,
                    'account_details' => $request->account_details
                ]
            ]);
            
            // Commit transaction
            DB::commit();
            
            // TODO: Notify admin about the new payout request
            
            return redirect()->route('facility.wallet.payouts.show', $payout)
                ->with('success', 'Payout request submitted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payout request failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to process payout request. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Show a specific payout request
     */
    public function showPayout(PayoutRequest $payout)
    {
        $facility = Auth::user()->facility;
        
        // Ensure the payout belongs to the facility
        if ($payout->facility_id !== $facility->id) {
            abort(403);
        }
        
        $wallet = $facility->wallet;
        
        // Get related transaction
        $transaction = $wallet->transactions()
            ->where('reference', $payout->reference)
            ->first();
        
        return view('medical_facilities.wallet.payout_show', [
            'payout' => $payout,
            'wallet' => $wallet,
            'facility' => $facility,
            'transaction' => $transaction
        ]);
    }
    
    /**
     * Show wallet top-up form
     */
    public function showTopUpForm()
    {
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        // Get saved payment methods if any
        $savedCards = $facility->paymentMethods()
            ->where('payment_method', 'card')
            ->where('is_active', true)
            ->get();
        
        return view('medical_facilities.wallet.top-up', [
            'wallet' => $wallet,
            'facility' => $facility,
            'savedCards' => $savedCards
        ]);
    }
    
    /**
     * Process wallet top-up
     */
    public function processTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100', // Minimum KES 100
            'payment_method' => 'required|in:mpesa,card,bank_transfer',
            'phone_number' => 'required_if:payment_method,mpesa|string|max:20',
            'card_id' => 'required_if:payment_method,card|exists:facility_payment_methods,id',
            'save_card' => 'boolean',
            'card_number' => 'required_if:payment_method,card|string|size:16',
            'expiry' => 'required_if:payment_method,card|string|size:5',
            'cvv' => 'required_if:payment_method,card|string|min:3|max:4',
            'card_name' => 'required_if:payment_method,card|string|max:100',
            'bank_receipt' => 'required_if:payment_method,bank_transfer|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);
        
        $facility = Auth::user()->facility;
        $wallet = $facility->wallet;
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            $reference = 'TOPUP-' . strtoupper(uniqid());
            $metadata = [
                'payment_method' => $request->payment_method,
                'initiated_at' => now()->toDateTimeString(),
            ];
            
            // Handle different payment methods
            switch ($request->payment_method) {
                case 'mpesa':
                    // Save payment method if requested
                    if ($request->boolean('save_card')) {
                        $facility->paymentMethods()->updateOrCreate(
                            ['nickname' => 'M-Pesa ' . substr($request->phone_number, -4)],
                            [
                                'payment_method' => 'mpesa',
                                'account_details' => $request->phone_number,
                                'is_default' => false,
                                'is_active' => true
                            ]
                        );
                    }
                    
                    // TODO: Initiate M-Pesa STK push
                    $metadata['phone_number'] = $request->phone_number;
                    $metadata['mpesa_request_id'] = 'MPESA-' . uniqid();
                    break;
                    
                case 'card':
                    // Save card if requested
                    if ($request->boolean('save_card') || $request->filled('card_id')) {
                        $cardData = [
                            'last_four' => substr($request->card_number, -4),
                            'exp_month' => explode('/', $request->expiry)[0],
                            'exp_year' => '20' . explode('/', $request->expiry)[1],
                            'card_name' => $request->card_name,
                            'is_default' => false,
                            'is_active' => true
                        ];
                        
                        if ($request->filled('card_id')) {
                            $card = $facility->paymentMethods()->findOrFail($request->card_id);
                            $card->update($cardData);
                        } else {
                            $facility->paymentMethods()->create(array_merge(
                                [
                                    'payment_method' => 'card',
                                    'nickname' => $request->card_name . ' ' . substr($request->card_number, -4),
                                    'account_details' => encrypt(json_encode([
                                        'number' => $request->card_number,
                                        'expiry' => $request->expiry,
                                        'cvv' => $request->cvv,
                                        'name' => $request->card_name
                                    ]))
                                ],
                                $cardData
                            ));
                        }
                    }
                    
                    // TODO: Process card payment
                    $metadata['card_last_four'] = substr($request->card_number, -4);
                    $metadata['payment_intent_id'] = 'pi_' . strtolower(Str::random(24));
                    break;
                    
                case 'bank_transfer':
                    // Handle bank transfer receipt upload
                    if ($request->hasFile('bank_receipt')) {
                        $path = $request->file('bank_receipt')->store('receipts/' . $facility->id, 'public');
                        $metadata['receipt_path'] = $path;
                        $metadata['uploaded_at'] = now()->toDateTimeString();
                    }
                    break;
            }
            
            // Create pending transaction
            $transaction = $wallet->transactions()->create([
                'amount' => $request->amount,
                'type' => 'topup',
                'description' => 'Wallet top-up via ' . ucfirst($request->payment_method),
                'reference' => $reference,
                'status' => 'pending',
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance,
                'metadata' => $metadata
            ]);
            
            // If payment method is bank transfer, don't update balance yet
            if ($request->payment_method !== 'bank_transfer') {
                // For demo, we'll mark as completed immediately
                // In production, this would happen after webhook confirmation
                $wallet->increment('balance', $request->amount);
                
                $transaction->update([
                    'status' => 'completed',
                    'balance_after' => $wallet->balance,
                    'metadata' => array_merge($metadata, [
                        'completed_at' => now()->toDateTimeString(),
                        'confirmed_by' => 'auto'
                    ])
                ]);
                
                // Redirect to success page
                return redirect()->route('facility.wallet.top-up.success', $transaction)
                    ->with('success', 'Your wallet has been topped up successfully!');
            }
            
            // For bank transfers, show pending message
            DB::commit();
            
            return redirect()->route('facility.wallet.top-up.success', $transaction)
                ->with('info', 'Your top-up request has been received. Your wallet will be updated once we confirm your bank transfer.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Top-up failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to process top-up. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Show top-up success page
     */
    public function topUpSuccess(FacilityWalletTransaction $transaction)
    {
        // Verify transaction belongs to the facility
        if ($transaction->wallet->facility_id !== Auth::user()->facility->id) {
            abort(403);
        }
        
        return view('medical_facilities.wallet.top_up_success', [
            'transaction' => $transaction,
            'wallet' => $transaction->wallet,
            'facility' => Auth::user()->facility
        ]);
    }
    
    /**
     * Show top-up cancelled page
     */
    public function topUpCancelled()
    {
        return view('medical_facilities.wallet.top_up_cancelled', [
            'wallet' => Auth::user()->facility->wallet,
            'facility' => Auth::user()->facility
        ]);
    }
    
    /**
     * Handle M-Pesa webhook
     */
    public function handleMpesaWebhook(Request $request)
    {
        // Verify webhook signature if needed
        
        $payload = $request->all();
        \Log::info('M-Pesa Webhook:', $payload);
        
        // TODO: Process M-Pesa webhook and update transaction status
        // This would typically be called by the payment gateway
        
        return response()->json(['status' => 'received']);
    }
    
    /**
     * Handle Stripe webhook
     */
    public function handleStripeWebhook(Request $request)
    {
        // Verify webhook signature if needed
        
        $payload = $request->all();
        \Log::info('Stripe Webhook:', $payload);
        
        // TODO: Process Stripe webhook and update transaction status
        // This would typically be called by the payment gateway
        
        return response()->json(['status' => 'received']);
    }
    
    /**
     * Cancel a payout request
     */
    public function cancelPayout(Request $request, PayoutRequest $payout)
    {
        // Verify the payout belongs to the facility
        if ($payout->facility_id !== Auth::user()->facility->id) {
            abort(403);
        }
        
        // Only allow cancelling pending payouts
        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending payouts can be cancelled.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update payout status
            $payout->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'cancellation_reason' => $request->input('reason', 'Cancelled by facility')
            ]);
            
            // Refund the amount to the wallet
            $wallet = $payout->facility->wallet;
            $wallet->increment('balance', $payout->amount);
            
            // Record the refund transaction
            $wallet->transactions()->create([
                'amount' => $payout->amount,
                'type' => 'refund',
                'description' => 'Refund for cancelled payout #' . $payout->id,
                'reference' => 'RFD-' . strtoupper(uniqid()),
                'status' => 'completed',
                'balance_before' => $wallet->balance - $payout->amount,
                'balance_after' => $wallet->balance,
                'metadata' => [
                    'payout_id' => $payout->id,
                    'cancelled_by' => Auth::id(),
                    'reason' => $request->input('reason')
                ]
            ]);
            
            // Update the original payout transaction if exists
            $wallet->transactions()
                ->where('reference', $payout->reference)
                ->update([
                    'status' => 'cancelled',
                    'metadata' => array_merge(
                        $wallet->transactions()->where('reference', $payout->reference)->first()->metadata ?? [],
                        [
                            'cancelled_at' => now()->toDateTimeString(),
                            'cancelled_by' => Auth::id(),
                            'cancellation_reason' => $request->input('reason')
                        ]
                    )
                ]);
            
            DB::commit();
            
            // TODO: Send notification to admin
            
            return redirect()->route('facility.wallet.payouts.show', $payout)
                ->with('success', 'Payout request has been cancelled successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to cancel payout: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to cancel payout. Please try again.');
        }
    }
}
