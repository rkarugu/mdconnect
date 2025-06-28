<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LocumShift;

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

    public function locumShift()
    {
        return $this->belongsTo(LocumShift::class, 'shift_id');
    }

    public function medicalWorker()
    {
        return $this->belongsTo(MedicalWorker::class);
    }
}
