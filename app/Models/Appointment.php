<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use SoftDeletes, HasUUID, MultiTenant, Auditable;

    protected $fillable = [
        'uuid',
        'branch_id',
        'patient_id',
        'doctor_id',
        'visit_id',
        'scheduled_at',
        'type',
        'status',
        'reason',
        'online_meeting_link'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'type' => 'string',
        'status' => 'string',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
