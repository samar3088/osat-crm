<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'team_id');
    }

    // ── Helpers ───────────────────────────────────────

    public function subAdmin(): ?User
    {
        return $this->members()->role('sub_admin')->first();
    }

    public function opsAdmin(): ?User
    {
        return $this->members()->role('operations_admin')->first();
    }

    public function teamMembers()
    {
        return $this->members()->role('team_member');
    }
}