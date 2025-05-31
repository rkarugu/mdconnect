<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LocumJobRequest;
use App\Models\MedicalWorker;

class JobApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'locum_job_request_id',
        'medical_worker_id',
        'bid_amount',
        'cover_note',
        'status',
        'rejection_reason',
        'availability',
        'responded_at',
        'accepted_at',
        'rejected_at',
        'withdrawn_at',
        'ranking_score',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'availability' => 'array',
        'responded_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'ranking_score' => 'integer',
    ];

    /**
     * Get the job request that owns the application.
     */
    public function jobRequest(): BelongsTo
    {
        return $this->belongsTo(LocumJobRequest::class, 'locum_job_request_id');
    }

    /**
     * Get the medical worker that owns the application.
     */
    public function medicalWorker(): BelongsTo
    {
        return $this->belongsTo(MedicalWorker::class);
    }

    /**
     * Check if the application is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' || $this->status === 'viewed' || $this->status === 'shortlisted';
    }

    /**
     * Update the application status.
     */
    public function updateStatus(string $status, ?string $reason = null): void
    {
        $updates = [
            'status' => $status,
            'responded_at' => now(),
        ];

        if ($reason) {
            $updates['rejection_reason'] = $reason;
        }

        switch ($status) {
            case 'accepted':
                $updates['accepted_at'] = now();
                break;
            case 'rejected':
                $updates['rejected_at'] = now();
                break;
            case 'withdrawn':
                $updates['withdrawn_at'] = now();
                break;
        }

        $this->update($updates);
    }

    /**
     * Calculate difference between the bid amount and job's hourly rate.
     */
    public function calculateBidDifference(): float
    {
        return $this->bid_amount - $this->jobRequest->hourly_rate;
    }

    /**
     * Check if this application's bid is below the job's hourly rate.
     */
    public function isBidBelow(): bool
    {
        return $this->bid_amount < $this->jobRequest->hourly_rate;
    }
}
