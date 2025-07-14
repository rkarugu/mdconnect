<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'finance-admin']);
    }

    public function view(User $user, Wallet $wallet): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('finance-admin')) {
            // finance admin can view worker wallets under their facility
            return $wallet->medicalWorker?->facility_id === $user->facility_id;
        }
        return false;
    }
}
