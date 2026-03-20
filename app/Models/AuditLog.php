<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ── Relationships ──────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper — log an action easily ──────────

    public static function record(string $action, string $module, string $description, array $old = [], array $new = []): void
    {
        self::create([
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()?->name,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'old_values'  => $old,
            'new_values'  => $new,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}