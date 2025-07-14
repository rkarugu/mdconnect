<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function show(Request $request)
    {
        $wallet = $request->user()->wallet ?? null;
        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $wallet?->balance ?? 0,
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $wallet = $request->user()->wallet;
        $txns = $wallet?->transactions()->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $txns]);
    }

    public function requestPayout(Request $request)
    {
        // TODO: implement payout flow/migration
        return response()->json(['success' => false, 'message' => 'Payout flow not implemented yet'], 501);
    }
}
