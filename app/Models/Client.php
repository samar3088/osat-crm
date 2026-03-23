<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_name',
        'client_pan',
        'client_mobile',
        'client_email',
        'client_type',
        'sales_category',
        'scheme_category',
        'transaction',
        'equity',
        'debt',
        'hybrid',
        'liquid',
        'sip_amount',
        'lumpsum_amount',
        'source_detail',
        'full_remarks',
        'latest_remarks',
        'latest_remarks_updated_on',
        'client_existing',
        'assigned_to',
        'service_team_id',
        'created_by',
        'is_active',
        'date_first_added',
        'date_of_birth',
    ];

    protected $casts = [
        'client_existing'           => 'boolean',
        'is_active'                 => 'boolean',
        'date_first_added'          => 'date',
        'latest_remarks_updated_on' => 'datetime',
        'equity'                    => 'decimal:2',
        'debt'                      => 'decimal:2',
        'hybrid'                    => 'decimal:2',
        'liquid'                    => 'decimal:2',
        'sip_amount'                => 'decimal:2',
        'lumpsum_amount'            => 'decimal:2',
        'date_of_birth' => 'date',
    ];

    // ── Relationships ──────────────────────────

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function serviceTeam()
    {
        return $this->belongsTo(User::class, 'service_team_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function sips()
    {
        return $this->hasMany(ClientSip::class);
    }

    public function aumSnapshots()
    {
        return $this->hasMany(ClientAumSnapshot::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}