<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Models\MedicalSpecialty;
use App\Models\MedicalDocument;
use App\Models\Shift;

class MedicalWorker extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'medical_specialty_id',
        'license_number',
        'years_of_experience',
        'bio',
        'education',
        'certifications',
        'status',
        'status_reason',
        'is_available',
        'working_hours',
        'phone',
        'profile_picture',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'working_hours' => 'array',
        'approved_at' => 'datetime',
        'last_status_change' => 'datetime',
        'email_verified_at' => 'datetime',
        'password_change_required' => 'boolean',
    ];

    protected $dates = [
        'approved_at',
        'last_status_change',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['password_change_required'];

    /**
     * Get the password_change_required attribute.
     *
     * @return bool
     */
    public function getPasswordChangeRequiredAttribute(): bool
    {
        return (bool) ($this->attributes['password_change_required'] ?? false);
    }

    /**
     * Get the user associated with this medical worker.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialty of the medical worker.
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(MedicalSpecialty::class, 'medical_specialty_id');
    }

    /**
     * Get the documents for the medical worker.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(MedicalDocument::class);
    }

    /**
     * Get the shifts associated with this medical worker.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }



    /**
     * Scope a query to only include approved medical workers.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include available medical workers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Check if the medical worker is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the medical worker is available.
     */
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    /**
     * Update the medical worker's status.
     */
    public function updateStatus(string $status, ?string $reason = null): void
    {
        $this->update([
            'status' => $status,
            'status_reason' => $reason,
            'last_status_change' => now(),
            'approved_at' => $status === 'approved' ? now() : $this->approved_at,
        ]);
    }
}
