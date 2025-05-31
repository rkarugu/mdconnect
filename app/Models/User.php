<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Single role relationship (since you use role_id)
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role by name.
     */
    public function hasRole(string $roleName): bool
    {
        if ($roleName === 'Admin' && optional($this->role)->name === 'Super Admin') {
            return true;
        }
        return optional($this->role)->name === $roleName;
    }
    
    /**
     * Assign a role to the user by role name.
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->role_id = $role->id;
            $this->save();
        }
    }

    /**
     * Get the medical worker profile associated with the user.
     */
    public function medicalWorker(): HasOne
    {
        return $this->hasOne(MedicalWorker::class);
    }
    
    /**
     * Get the medical facility profile associated with the user.
     */
    public function medicalFacility(): HasOne
    {
        return $this->hasOne(MedicalFacility::class);
    }
}
