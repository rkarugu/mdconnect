<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\FacilityDocument;
use App\Models\LocumJobRequest;

class MedicalFacility extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'facility_name',
        'facility_type',
        'license_number',
        'tax_id',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'description',
        'bed_capacity',
        'status',
        'status_reason',
        'is_active',
        'operating_hours',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'operating_hours' => 'array',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'last_status_change' => 'datetime',
    ];

    protected $dates = [
        'verified_at',
        'approved_at',
        'last_status_change',
    ];

    /**
     * Get the user that owns the facility.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the documents for the medical facility.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(FacilityDocument::class);
    }

    /**
     * Get the locum job requests posted by this facility.
     */
    public function jobRequests(): HasMany
    {
        return $this->hasMany(LocumJobRequest::class);
    }

    /**
     * Check if the medical facility is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the medical facility is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Update the medical facility's status.
     */
    public function updateStatus(string $status, ?string $reason = null): void
    {
        $this->update([
            'status' => $status,
            'status_reason' => $reason,
            'last_status_change' => now(),
            'verified_at' => $status === 'verified' ? now() : $this->verified_at,
            'approved_at' => $status === 'approved' ? now() : $this->approved_at,
        ]);
    }
}
