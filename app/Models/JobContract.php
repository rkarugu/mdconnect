<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LocumJobRequest;
use App\Models\MedicalWorker;
use App\Models\JobApplication;

class JobContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'locum_job_request_id',
        'medical_worker_id',
        'job_application_id',
        'contract_number',
        'status',
        'hourly_rate',
        'terms_and_conditions',
        'cancellation_policy',
        'has_nda',
        'nda_details',
        'facility_signature_path',
        'worker_signature_path',
        'facility_signed_at',
        'worker_signed_at',
        'contract_start',
        'contract_end',
        'contract_document_path',
        'total_hours',
        'total_amount',
        'payment_status',
        'payment_history',
        'shift_schedule',
        'cancelled_at',
        'cancellation_reason',
        'dispute_filed_at',
        'dispute_reason',
        'dispute_resolved_at',
        'dispute_resolution',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'terms_and_conditions' => 'array',
        'payment_history' => 'array',
        'shift_schedule' => 'array',
        'has_nda' => 'boolean',
        'facility_signed_at' => 'datetime',
        'worker_signed_at' => 'datetime',
        'contract_start' => 'datetime',
        'contract_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'dispute_filed_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
    ];

    /**
     * Generate a unique contract number.
     */
    public static function generateContractNumber(): string
    {
        $prefix = 'CNT';
        $timestamp = now()->format('YmdHis');
        $random = rand(1000, 9999);
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    /**
     * Get the job request associated with the contract.
     */
    public function jobRequest(): BelongsTo
    {
        return $this->belongsTo(LocumJobRequest::class, 'locum_job_request_id');
    }

    /**
     * Get the medical worker associated with the contract.
     */
    public function medicalWorker(): BelongsTo
    {
        return $this->belongsTo(MedicalWorker::class);
    }

    /**
     * Get the job application associated with the contract.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    /**
     * Check if the contract is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the contract is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the contract is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the contract is disputed.
     */
    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    /**
     * Check if the contract is fully signed.
     */
    public function isFullySigned(): bool
    {
        return !is_null($this->facility_signed_at) && !is_null($this->worker_signed_at);
    }

    /**
     * Update the contract status.
     */
    public function updateStatus(string $status, ?string $reason = null): void
    {
        $updates = ['status' => $status];

        switch ($status) {
            case 'cancelled':
                $updates['cancelled_at'] = now();
                $updates['cancellation_reason'] = $reason;
                break;
            case 'disputed':
                $updates['dispute_filed_at'] = now();
                $updates['dispute_reason'] = $reason;
                break;
            case 'completed':
                // Calculate final hours and amount if not already set
                if ($this->total_hours == 0) {
                    $this->calculateTotalHoursAndAmount();
                }
                break;
        }

        $this->update($updates);
    }

    /**
     * Record worker signature.
     */
    public function signByWorker(string $signaturePath): void
    {
        $newStatus = $this->facility_signed_at ? 'active' : 'signed_worker';
        
        $this->update([
            'worker_signature_path' => $signaturePath,
            'worker_signed_at' => now(),
            'status' => $newStatus
        ]);
    }

    /**
     * Record facility signature.
     */
    public function signByFacility(string $signaturePath): void
    {
        $newStatus = $this->worker_signed_at ? 'active' : 'signed_facility';
        
        $this->update([
            'facility_signature_path' => $signaturePath,
            'facility_signed_at' => now(),
            'status' => $newStatus
        ]);
    }

    /**
     * Calculate total hours and amount based on the contract.
     */
    public function calculateTotalHoursAndAmount(): void
    {
        // For simple non-recurring contracts
        if (!$this->jobRequest->is_recurring) {
            $hours = $this->jobRequest->calculateShiftDuration();
            $amount = $hours * $this->hourly_rate;
        } else {
            // For recurring contracts, calculate based on completed shifts in shift_schedule
            $hours = 0;
            $completedShifts = collect($this->shift_schedule)->where('status', 'completed');
            
            foreach ($completedShifts as $shift) {
                $start = \Carbon\Carbon::parse($shift['start_time']);
                $end = \Carbon\Carbon::parse($shift['end_time']);
                $hours += $end->diffInHours($start);
            }
            
            $amount = $hours * $this->hourly_rate;
        }
        
        $this->update([
            'total_hours' => $hours,
            'total_amount' => $amount
        ]);
    }

    /**
     * Record a payment to the contract.
     */
    public function recordPayment(float $amount, string $transactionId, string $method): void
    {
        $paymentHistory = $this->payment_history ?? [];
        
        $paymentHistory[] = [
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'method' => $method,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        $totalPaid = collect($paymentHistory)->sum('amount');
        
        $this->update([
            'payment_history' => $paymentHistory,
            'payment_status' => $totalPaid >= $this->total_amount ? 'completed' : 'partial'
        ]);
    }
}
