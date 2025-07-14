<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'status',
        'requested_at',
        'processed_at',
        'processed_by',
    ];

    protected $dates = ['requested_at', 'processed_at'];

    /* Relationships */
    public function wallet()
    {
        return $this->belongsTo(\App\Models\FacilityWallet::class, 'wallet_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
