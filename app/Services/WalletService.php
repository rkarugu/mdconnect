<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\FacilityWallet;
use App\Models\FacilityWalletTransaction;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    public function __construct(private DatabaseManager $db)
    {
    }

    /* ---------------------------- Worker Wallets ---------------------------*/

    public function creditWorker(Wallet $wallet, float $amount, string $refType = null, int $refId = null, array $meta = []): WalletTransaction
    {
        return $this->db->transaction(function () use ($wallet, $amount, $refType, $refId, $meta) {
            $wallet->refresh()->lockForUpdate();
            $wallet->balance += $amount;
            $wallet->save();

            return $wallet->transactions()->create([
                'type'           => 'credit',
                'amount'         => $amount,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'meta'           => $meta,
            ]);
        });
    }

    public function debitWorker(Wallet $wallet, float $amount, string $refType = null, int $refId = null, array $meta = []): WalletTransaction
    {
        return $this->db->transaction(function () use ($wallet, $amount, $refType, $refId, $meta) {
            $wallet->refresh()->lockForUpdate();
            if ($wallet->balance < $amount) {
                throw new InvalidArgumentException('Insufficient balance');
            }
            $wallet->balance -= $amount;
            $wallet->save();

            return $wallet->transactions()->create([
                'type'           => 'debit',
                'amount'         => $amount,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'meta'           => $meta,
            ]);
        });
    }

    /* --------------------------- Facility Wallets --------------------------*/

    public function debitFacility(FacilityWallet $wallet, float $amount, string $refType = null, int $refId = null, array $meta = []): FacilityWalletTransaction
    {
        return $this->db->transaction(function () use ($wallet, $amount, $refType, $refId, $meta) {
            $wallet->refresh()->lockForUpdate();
            if ($wallet->balance < $amount) {
                throw new InvalidArgumentException('Insufficient balance');
            }
            $wallet->balance -= $amount;
            $wallet->save();

            return $wallet->transactions()->create([
                'type'           => 'debit',
                'amount'         => $amount,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'meta'           => $meta,
            ]);
        });
    }

    public function creditFacility(FacilityWallet $wallet, float $amount, string $refType = null, int $refId = null, array $meta = []): FacilityWalletTransaction
    {
        return $this->db->transaction(function () use ($wallet, $amount, $refType, $refId, $meta) {
            $wallet->refresh()->lockForUpdate();
            $wallet->balance += $amount;
            $wallet->save();

            return $wallet->transactions()->create([
                'type'           => 'credit',
                'amount'         => $amount,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'meta'           => $meta,
            ]);
        });
    }

    /* ---------------------------- Holds & Release --------------------------*/

    public function holdFacility(FacilityWallet $wallet, float $amount, string $refType = null, int $refId = null, array $meta = []): FacilityWalletTransaction
    {
        // No balance change for hold; just record
        return $wallet->transactions()->create([
            'type'           => 'hold',
            'amount'         => $amount,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'meta'           => $meta,
        ]);
    }

    public function convertHoldToDebit(FacilityWalletTransaction $holdTxn): FacilityWalletTransaction
    {
        if ($holdTxn->type !== 'hold') {
            throw new InvalidArgumentException('Transaction not a hold');
        }

        return DB::transaction(function () use ($holdTxn) {
            $wallet = $holdTxn->wallet()->lockForUpdate()->first();
            $wallet->balance -= $holdTxn->amount; // assuming funds captured elsewhere
            $wallet->save();

            $holdTxn->update(['type' => 'debit']);

            return $holdTxn;
        });
    }
}
