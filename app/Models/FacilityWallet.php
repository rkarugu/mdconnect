<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FacilityWalletTransaction;

class FacilityWallet extends Model
{
    protected $fillable = ['medical_facility_id', 'balance'];

    public function medicalFacility(): BelongsTo
    {
        return $this->belongsTo(MedicalFacility::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FacilityWalletTransaction::class);
    }
}
