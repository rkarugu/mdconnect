<?php

namespace App\Policies;

use App\Models\FacilityWallet;
use App\Models\User;

class FacilityWalletPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'finance-admin']);
    }

    public function view(User $user, FacilityWallet $wallet): bool
    {
        return $user->hasRole('super-admin') || ($user->hasRole('finance-admin') && $user->facility_id === $wallet->medical_facility_id);
    }
}
