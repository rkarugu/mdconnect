<?php

namespace App\Http\Controllers\Api\Facility;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;

class FacilityWalletController extends Controller
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function show(Request $request)
    {
        $wallet = $request->user()->facility?->wallet;
        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $wallet?->balance ?? 0,
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $wallet = $request->user()->facility?->wallet;
        $txns = $wallet?->transactions()->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $txns]);
    }

    public function topUp(Request $request)
    {
        // TODO: implement top-up integration with payment gateway
        return response()->json(['success' => false, 'message' => 'Top-up not implemented yet'], 501);
    }
}
