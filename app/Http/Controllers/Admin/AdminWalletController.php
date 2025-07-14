<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityWallet;
use App\Models\Wallet;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;

use App\Models\ActivityLog;
use App\Notifications\PayoutStatusChanged;
use App\Services\WalletService;
use Carbon\Carbon;

class AdminWalletController extends Controller
{
    /**
     * Display global wallet stats dashboard.
     */
    public function index()
    {
        return view('admin.wallets.dashboard', [
            'facilities_balance' => FacilityWallet::sum('balance'),
            'workers_balance'    => Wallet::sum('balance'),
            'pending_payouts'    => PayoutRequest::where('status', 'pending')->count(),
        ]);
    }

    /**
     * Facility wallet & transactions.
     */
    public function facility($id)
    {
        $wallet = FacilityWallet::where('medical_facility_id', $id)->with('transactions')->firstOrFail();
        return view('admin.wallets.facility', compact('wallet'));
    }

    /**
     * Worker wallet & transactions.
     */
    public function worker($id)
    {
        $wallet = Wallet::where('medical_worker_id', $id)->with('transactions')->firstOrFail();
        return view('admin.wallets.worker', compact('wallet'));
    }

    /**
     * List payout requests.
     */
    public function payouts()
    {
        $requests = PayoutRequest::latest()->paginate(20);
        return view('admin.wallets.payouts', compact('requests'));
    }

    /**
     * Approve a payout request.
     */
    public function approve(PayoutRequest $payout, WalletService $walletService)
    {
        if ($payout->status !== 'pending') {
            return back()->withErrors('Payout already processed');
        }

        // Debit worker wallet
        $walletService->debitWorker($payout->wallet, $payout->amount, 'payout', $payout->id);

        $payout->update([
            'status'       => 'paid',
            'processed_at' => Carbon::now(),
            'processed_by' => auth()->id(),
        ]);

        // Notify worker
        $payout->wallet->medicalWorker->notify(new PayoutStatusChanged($payout));

        // Log activity
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'payout_approved',
            'subject_type' => PayoutRequest::class,
            'subject_id'   => $payout->id,
            'description'  => 'Payout approved',
        ]);

        return back()->with('status', 'Payout approved');
    }

    /**
     * Reject payout request.
     */
    public function reject(PayoutRequest $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->withErrors('Payout already processed');
        }

        $payout->update([
            'status'       => 'rejected',
            'processed_at' => Carbon::now(),
            'processed_by' => auth()->id(),
        ]);

        $payout->wallet->medicalWorker->notify(new PayoutStatusChanged($payout));

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'payout_rejected',
            'subject_type' => PayoutRequest::class,
            'subject_id'   => $payout->id,
            'description'  => 'Payout rejected',
        ]);

        return back()->with('status', 'Payout rejected');
    }
}
