<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocumShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'medical_worker_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'actual_start_time',
        'actual_end_time',
        'location',
        'worker_type',
        'slots_available',
        'pay_rate',
        'status',
        'auto_match',
        'instant_book',
        'created_by',
        'ended_at',
        'ended_by',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'ended_at' => 'datetime',
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

    /**
     * Get the user who ended the shift.
     */
    public function endedBy()
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    public function applications()
    {
        return $this->hasMany(ShiftApplication::class, 'shift_id');
    }

    /**
     * Calculate the duration of the shift in minutes
     */
    public function getDurationMinutesAttribute()
    {
        if (!$this->actual_start_time || !$this->actual_end_time) {
            return null;
        }

        return $this->actual_start_time->diffInMinutes($this->actual_end_time);
    }

    /**
     * Get formatted duration display (e.g., "2h 30m")
     */
    public function getDurationDisplayAttribute()
    {
        $minutes = $this->duration_minutes;
        if (!$minutes) {
            return null;
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours}h {$remainingMinutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$remainingMinutes}m";
        }
    }

    /**
     * Check if all workers have completed their shift applications
     */
    public function allWorkersCompleted()
    {
        $totalApplications = $this->applications()->where('status', 'approved')->count();
        $completedApplications = $this->applications()->where('status', 'completed')->count();
        
        return $totalApplications > 0 && $totalApplications === $completedApplications;
    }

    /**
     * Update shift timing when first worker starts
     */
    public function updateStartTime()
    {
        if (!$this->actual_start_time) {
            $this->update(['actual_start_time' => now()]);
        }
    }

    /**
     * Update shift timing when all workers complete
     */
    public function updateEndTime()
    {
        if ($this->allWorkersCompleted() && !$this->actual_end_time) {
            $this->update([
                'actual_end_time' => now(),
                'status' => 'completed'
            ]);
        }
    }
}
