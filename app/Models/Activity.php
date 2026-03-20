<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'client_name',
        'client_pan',
        'client_mobile',
        'client_email',
        'client_type',
        'sales_category',
        'scheme_category',
        'transaction',
        'amount',
        'remarks',
        'full_remarks',
        'activity_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'is_active'     => 'boolean',
        'activity_date' => 'datetime',
    ];

    // ── Relationships ──────────────────────────

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}