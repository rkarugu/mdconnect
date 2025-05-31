<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MedicalWorker;
use App\Models\User;

class MedicalDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'medical_worker_id',
        'document_type',
        'title',
        'file_path',
        'mime_type',
        'file_size',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the medical worker that owns the document.
     */
    public function medicalWorker(): BelongsTo
    {
        return $this->belongsTo(MedicalWorker::class);
    }

    /**
     * Get the user who verified the document.
     */
    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the document is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === 'approved' && !is_null($this->verified_at);
    }

    /**
     * Update the document's verification status.
     */
    public function updateVerificationStatus(string $status, ?string $reason = null, ?int $verifiedBy = null): void
    {
        $this->update([
            'status' => $status,
            'rejection_reason' => $reason,
            'verified_at' => $status === 'approved' ? now() : null,
            'verified_by' => $status === 'approved' ? $verifiedBy : null,
        ]);
    }
}
