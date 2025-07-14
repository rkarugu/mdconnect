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
        'medical_specialty_id',
        'profile_picture',
    ];
    
    /**
     * Get the facility associated with the user (if the user is a facility admin).
     */
    public function facility(): HasOne
    {
        return $this->hasOne(MedicalFacility::class, 'user_id');
    }

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

    public function medicalSpecialty(): BelongsTo
    {
        return $this->belongsTo(MedicalSpecialty::class);
    }

    /**
     * Check if user has a specific role by name.
     */
    public function hasRole(string $roleName): bool
    {
        // Normalize both the expected role name and the user's role name to lowercase for comparison
        $userRoleName = strtolower(optional($this->role)->name);
        $expectedRoleName = str_replace('-', ' ', strtolower($roleName));

        // Special handling for 'Admin' if 'Super Admin' is present
        if ($expectedRoleName === 'admin' && $userRoleName === 'super admin') {
            return true;
        }

        return $userRoleName === $expectedRoleName;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        $userRoleName = strtolower(optional($this->role)->name);
        if (!$userRoleName) {
            return false;
        }

        foreach ($roles as $roleName) {
            $expectedRoleName = str_replace('-', ' ', strtolower($roleName));
            if ($userRoleName === $expectedRoleName) {
                return true;
            }
            // Allow admin to access super-admin things
            if ($expectedRoleName === 'admin' && $userRoleName === 'super admin') {
                return true;
            }
        }

        return false;
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
