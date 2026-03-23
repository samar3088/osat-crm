<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'employee_code',
        'work_type',
        'password',
        'is_active',
        'assigned_to',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ── Role Helpers ───────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isTeamMember(): bool
    {
        return $this->hasRole('team_member');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    // ── Relationships ──────────────────────────────

    /**
     * Who this user reports to (assigned manager)
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Team members assigned under this user
     */
    public function teamMembers()
    {
        return $this->hasMany(User::class, 'assigned_to');
    }

    /**
     * Clients assigned to this RM
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'assigned_to');
    }

    /**
     * Targets for this user
     */
    public function targets()
    {
        return $this->hasMany(UserTarget::class);
    }

    /**
     * Current month target
     */
    public function currentMonthTarget()
    {
        return $this->hasMany(UserTarget::class)
            ->where('year', now()->year)
            ->where('month', now()->month);
    }

    /**
     * Conveyances submitted by this user
     */
    public function conveyances()
    {
        return $this->hasMany(Conveyance::class);
    }

    /**
     * Activities logged by this user
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    /**
     * Notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Unread notifications count
     */
    public function unreadNotificationsCount()
    {
        return $this->hasMany(Notification::class)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Meetings scheduled for this user
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Audit logs for this user
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Team Relationship ─────────────────────────────
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // ── Role Helpers (add alongside existing ones) ────
    public function isSubAdmin(): bool
    {
        return $this->hasRole('sub_admin');
    }

    public function isOpsAdmin(): bool
    {
        return $this->hasRole('operations_admin');
    }

    // ── Check if user can manage team ────────────────
    public function canManageTeam(Team $team): bool
    {
        if ($this->isSuperAdmin()) return true;
        if ($this->isSubAdmin() && $this->team_id === $team->id) return true;
        return false;
    }
}