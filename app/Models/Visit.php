<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Diagnosis;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Vital;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes, HasUUID, MultiTenant, Auditable;

    protected $fillable = [
        'uuid',
        'branch_id',
        'patient_id',
        'doctor_id',
        'queue_token',
        'visit_type',
        'status',
        'complaint',
        'notes',
        'bp',
        'pulse',
        'temp',
        'weight',
    ];

    protected $casts = [
        'visit_type' => 'string',
        'status' => 'string',
        'weight' => 'decimal:2',
        'temp' => 'decimal:1',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }



    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }

    /**
     * Get the patient associated with the visit.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor associated with the visit.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the last diagnosis.
     */
    public function latestDiagnosis()
    {
        return $this->hasOne(Diagnosis::class)->latestOfMany();
    }

    /**
     * Get the last prescription.
     */
    public function latestPrescription()
    {
        return $this->hasOne(Prescription::class)->latestOfMany();
    }

    /**
     * Scope a query to only include waiting visits.
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope a query to only include in-progress visits.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed visits.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled visits.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include today's visits.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Get visit duration in minutes.
     */
    public function getDurationAttribute()
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->completed_at);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return 'N/A';
        }

        if ($this->duration < 60) {
            return $this->duration . ' minutes';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        return $hours . 'h ' . $minutes . 'm';
    }

    /**
     * Check if visit is active (not completed or cancelled).
     */
    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['waiting', 'in_progress']);
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'waiting' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';

        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' .
            ucfirst(str_replace('_', ' ', $this->status)) .
            '</span>';
    }

    /**
     * Get formatted blood pressure.
     */
    public function getFormattedBpAttribute()
    {
        if (!$this->bp) {
            return 'N/A';
        }

        return $this->bp . ' mmHg';
    }

    /**
     * Get waiting time in minutes.
     */
    public function getWaitingTimeAttribute()
    {
        if ($this->status === 'completed' && $this->completed_at) {
            return $this->created_at->diffInMinutes($this->completed_at);
        }

        return $this->created_at->diffInMinutes(now());
    }

    /**
     * Get visit statistics.
     */
    public function getStatisticsAttribute()
    {
        return [
            'has_vitals' => $this->latestVital !== null,
            'has_diagnosis' => $this->diagnoses()->count() > 0,
            'has_prescriptions' => $this->prescriptions()->count() > 0,
            'total_diagnoses' => $this->diagnoses()->count(),
            'total_prescriptions' => $this->prescriptions()->count(),
            'is_urgent' => $this->latestVital && $this->latestVital->is_critical,
        ];
    }

    /**
     * Get the diagnoses for the visit.
     */
    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    /**
     * Get all prescriptions through diagnoses.
     */
    public function prescriptions()
    {
        return $this->hasManyThrough(Prescription::class, Diagnosis::class);
    }

    /**
     * Get next status in workflow.
     */
    public function getNextStatusAttribute()
    {
        $workflow = [
            'waiting' => 'in_progress',
            'in_progress' => 'completed',
            'completed' => null,
            'cancelled' => null,
        ];

        return $workflow[$this->status] ?? null;
    }

    /**
     * Check if visit can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['waiting', 'in_progress']);
    }

    /**
     * Mark visit as completed.
     */
    public function markAsCompleted()
    {
        if ($this->canBeCompleted()) {
            $this->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Check if visit can be completed.
     */
    public function canBeCompleted()
    {
        return $this->status === 'in_progress' && $this->diagnoses()->count() > 0;
    }

    /**
     * Add diagnosis to visit.
     */
    public function addDiagnosis($data)
    {
        return $this->diagnoses()->create(array_merge($data, [
            'patient_id' => $this->patient_id,
            'doctor_id' => auth()->id(),
        ]));
    }

    /**
     * Update or create vitals for visit.
     */
    public function updateVitals($data)
    {
        if ($this->vitals) {
            return $this->vitals()->update($data);
        }

        return $this->vitals()->create(array_merge($data, [
            'patient_id' => $this->patient_id,
            'staff_id' => auth()->id(),
            'date_time' => now(),
        ]));
    }

    /**
     * Get the vitals record associated with the visit.
     */
    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class);
    }

    /**
     * Get the latest vitals record associated with the visit.
     */
    public function latestVital(): HasOne
    {
        return $this->hasOne(Vital::class)->latestOfMany();
    }
}
