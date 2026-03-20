<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // 👈 Spatie roles trait

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // 👈 HasRoles added

    /**
     * Fields that can be mass assigned
     * (safe to fill via create() or update())
     */
    protected $fillable = [
        'name',
        'email',
        'employee_code',
        'password',
        'is_active',
        'assigned_to',
    ];

    /**
     * Fields hidden from arrays/JSON
     * (never expose password or remember token)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',  // Auto-hashes password on set
            'is_active'         => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────

    /**
     * Team member is assigned to a Super Admin or Branch Admin
     */
    public function assignedTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Super Admin / RM can have many customers assigned to them
     */
    public function assignedCustomers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'assigned_to');
    }

    // ── Helper Methods ─────────────────────────────

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is Team Member / RM
     */
    public function isTeamMember(): bool
    {
        return $this->hasRole('team_member');
    }

    /**
     * Check if user is Customer
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }
}