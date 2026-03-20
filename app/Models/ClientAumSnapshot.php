<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientAumSnapshot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'client_name',
        'client_pan',
        'client_email',
        'client_mobile',
        'aum',
        'employee_id',
        'employee_code',
        'registered_on',
    ];

    protected $casts = [
        'aum'           => 'decimal:2',
        'registered_on' => 'date',
    ];

    // ── Relationships ──────────────────────────

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}