<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['medical_worker_id', 'balance'];

    /**
     * Get the medical worker that owns the wallet.
     */
    public function medicalWorker(): BelongsTo
    {
        return $this->belongsTo(MedicalWorker::class);
    }

    /**
     * Transactions for this wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
