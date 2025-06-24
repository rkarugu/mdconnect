<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BidInvitation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shift_id',
        'medical_worker_id',
        'minimum_bid',
        'closes_at',
        'status',
    ];

    protected $casts = [
        'minimum_bid' => 'decimal:2',
        'closes_at' => 'datetime',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function medicalWorker()
    {
        return $this->belongsTo(MedicalWorker::class);
    }
}
