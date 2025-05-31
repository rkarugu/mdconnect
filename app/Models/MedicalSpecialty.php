<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MedicalWorker;

class MedicalSpecialty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'qualification_requirements',
        'minimum_experience_years',
        'is_active',
        'slug',
        'icon'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'minimum_experience_years' => 'integer',
    ];

    /**
     * Get the medical workers with this specialty.
     */
    public function medicalWorkers(): HasMany
    {
        return $this->hasMany(MedicalWorker::class, 'specialty_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($specialty) {
            if (empty($specialty->slug)) {
                $specialty->slug = str()->slug($specialty->name);
            }
        });

        static::updating(function ($specialty) {
            if ($specialty->isDirty('name')) {
                $specialty->slug = str()->slug($specialty->name);
            }
        });
    }
}
