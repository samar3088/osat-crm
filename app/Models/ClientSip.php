<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientSip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'client_name',
        'client_pan',
        'amount',
        'team_member_id',
        'employee_code',
        'is_active',
        'registered_on',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'is_active'     => 'boolean',
        'registered_on' => 'date',
    ];

    // ── Relationships ──────────────────────────

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function teamMember()
    {
        return $this->belongsTo(User::class, 'team_member_id');
    }
}