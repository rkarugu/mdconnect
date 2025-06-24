<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstantRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shift_id',
        'medical_worker_id',
        'hourly_rate',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'expires_at' => 'datetime',
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
