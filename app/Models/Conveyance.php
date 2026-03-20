<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conveyance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'conveyance_type',
        'amount',
        'remarks',
        'bill_path',
        'conveyance_date',
        'status',
        'actioned_by',
        'action_remarks',
        'actioned_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'conveyance_date' => 'date',
        'actioned_at'     => 'datetime',
    ];

    // ── Relationships ──────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    // ── Helpers ────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}