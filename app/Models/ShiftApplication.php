<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'medical_worker_id',
        'status',
        'applied_at',
        'selected_at',
    ];

    public function shift()
    {
        return $this->belongsTo(LocumShift::class, 'shift_id');
    }

    public function medicalWorker()
    {
        return $this->belongsTo(MedicalWorker::class);
    }
}
