<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MedicalFacility;
use App\Models\MedicalSpecialty;
use App\Models\JobApplication;
use App\Models\JobContract;

class LocumJobRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'medical_facility_id',
        'specialty_id',
        'title',
        'description',
        'required_experience_years',
        'required_qualifications',
        'responsibilities',
        'is_recurring',
        'shift_type',
        'shift_start',
        'shift_end',
        'recurring_pattern',
        'recurring_duration_days',
        'hourly_rate',
        'benefits',
        'is_remote',
        'location',
        'status',
        'slots_available',
        'auto_match_enabled',
        'instant_book_enabled',
        'posted_at',
        'deadline',
        'filled_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'required_qualifications' => 'array',
        'recurring_pattern' => 'array',
        'benefits' => 'array',
        'is_remote' => 'boolean',
        'auto_match_enabled' => 'boolean',
        'instant_book_enabled' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'slots_available' => 'integer',
        'required_experience_years' => 'integer',
        'recurring_duration_days' => 'integer',
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
        'posted_at' => 'datetime',
        'deadline' => 'datetime',
        'filled_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the medical facility that owns the job request.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(MedicalFacility::class, 'medical_facility_id');
    }

    /**
     * Get the medical specialty associated with the job request.
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(MedicalSpecialty::class, 'specialty_id');
    }

    /**
     * Get the applications for this job request.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the contracts for this job request.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(JobContract::class);
    }

    /**
     * Check if the job request is open for applications.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Check if the job request is filled.
     */
    public function isFilled(): bool
    {
        return $this->status === 'filled' || $this->status === 'completed';
    }

    /**
     * Check if the job request is recurring.
     */
    public function isRecurring(): bool
    {
        return $this->is_recurring;
    }

    /**
     * Get the accepted applications for this job request.
     */
    public function acceptedApplications()
    {
        return $this->applications()->where('status', 'accepted');
    }

    /**
     * Update the job request status.
     */
    public function updateStatus(string $status): void
    {
        $updates = ['status' => $status];

        switch ($status) {
            case 'filled':
                $updates['filled_at'] = now();
                break;
            case 'completed':
                $updates['completed_at'] = now();
                break;
            case 'cancelled':
                $updates['cancelled_at'] = now();
                break;
        }

        $this->update($updates);
    }

    /**
     * Calculate the total duration of the shift in hours.
     */
    public function calculateShiftDuration(): float
    {
        if (!$this->shift_start || !$this->shift_end) {
            return 0;
        }

        $start = $this->shift_start;
        $end = $this->shift_end;
        
        return $end->diffInHours($start);
    }

    /**
     * Calculate the total pay for the shift.
     */
    public function calculateTotalPay(): float
    {
        return $this->calculateShiftDuration() * $this->hourly_rate;
    }
}
