<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bid_invitation_id',
        'medical_worker_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function bidInvitation()
    {
        return $this->belongsTo(BidInvitation::class);
    }

    public function medicalWorker()
    {
        return $this->belongsTo(MedicalWorker::class);
    }
}
