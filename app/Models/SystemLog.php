<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'page',
        'detail',
        'stack_trace',
        'user_id',
        'username',
        'ip_address',
        'log_date',
        'logged_at',
    ];

    protected $casts = [
        'log_date'  => 'date',
        'logged_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper — log errors easily ─────────────

    public static function error(string $detail, string $page = '', ?\Throwable $e = null): void
    {
        self::create([
            'type'        => 'error',
            'page'        => $page,
            'detail'      => $detail,
            'stack_trace' => $e?->getTraceAsString(),
            'user_id'     => auth()->id(),
            'username'    => auth()->user()?->name,
            'ip_address'  => request()->ip(),
            'log_date'    => now()->toDateString(),
            'logged_at'   => now(),
        ]);
    }

    public static function info(string $detail, string $page = ''): void
    {
        self::create([
            'type'      => 'info',
            'page'      => $page,
            'detail'    => $detail,
            'user_id'   => auth()->id(),
            'username'  => auth()->user()?->name,
            'ip_address'=> request()->ip(),
            'log_date'  => now()->toDateString(),
            'logged_at' => now(),
        ]);
    }
}