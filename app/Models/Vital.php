<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Vital extends Model
{
    use HasUUID, MultiTenant, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'uuid',
        'branch_id',
        'patient_id',
        'visit_id',
        'recorded_by',
        'recorded_at',
        'temperature',
        'pulse',
        'respiratory_rate',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'oxygen_saturation',
        'oxygen_device',
        'oxygen_flow_rate',
        'pain_scale',
        'height',
        'weight',
        'bmi',
        'blood_glucose',
        'heart_rate',
        'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'decimal:1',
        'bmi' => 'decimal:1',
        'blood_glucose' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vital) {
            if (empty($vital->uuid)) {
                $vital->uuid = (string) Str::uuid();
            }
        });
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
