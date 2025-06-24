<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'hourly_rate',
        'location',
        'medical_worker_id',
        'status',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hourly_rate' => 'decimal:2',
    ];

    public function medicalWorker()
    {
        return $this->belongsTo(MedicalWorker::class);
    }
}
