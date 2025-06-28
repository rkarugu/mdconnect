<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocumShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'worker_type',
        'slots_available',
        'pay_rate',
        'status',
        'auto_match',
        'instant_book',
        'created_by',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'auto_match' => 'boolean',
        'instant_book' => 'boolean',
    ];

    /**
     * Expire shifts whose start time has passed but are still open/confirmed.
     */
    public static function expireDueShifts(): void
    {
        self::whereIn('status', ['open', 'confirmed'])
            ->where('end_datetime', '<=', now())
            ->update(['status' => 'expired']);
    }

    public function facility()
    {
        return $this->belongsTo(MedicalFacility::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(ShiftApplication::class, 'shift_id');
    }
}
