<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTarget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'plan',
        'type',
        'category',
        'target_amount',
        'target_amount_achieved',
        'target_investors',
        'target_investors_achieved',
    ];

    protected $casts = [
        'target_amount'             => 'decimal:2',
        'target_amount_achieved'    => 'decimal:2',
        'target_investors'          => 'integer',
        'target_investors_achieved' => 'integer',
    ];

    // ── Relationships ──────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────

    public function achievementPercentage(): float
    {
        if ($this->target_amount == 0) return 0;
        return round(($this->target_amount_achieved / $this->target_amount) * 100, 2);
    }
}