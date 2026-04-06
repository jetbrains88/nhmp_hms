<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Designation;
use App\Models\Diagnosis;
use App\Models\EmployeeDetail;
use App\Models\LabOrder;
use App\Models\Office;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Models\Vital;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes, HasUUID, MultiTenant, Auditable;


    protected $fillable = [
        'uuid',
        'branch_id',
        'cnic',
        'emrn',
        'name',
        'dob',
        'gender',
        'phone',
        'address',
        'emergency_contact',
        'allergies',
        'chronic_conditions',
        'medical_history',
        'blood_group',
        'is_active',
        'opd_year',
        'opd_sequence',
        'user_id',
        'parent_id',
        'relationship'
    ];

    protected $casts = [
        'dob' => 'date',
        'is_nhmp' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'opd_number',
        'formatted_cnic',
        'nhmp_badge',
        'nhmp_status',
        'age',
        'age_formatted',
        'formatted_phone',
    ];


    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Patient::class, 'parent_id');
    }

    public function employeeDetail(): HasOne
    {
        return $this->hasOne(EmployeeDetail::class);
    }





    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getOpdNumberAttribute(): ?string
    {
        if ($this->opd_year && $this->opd_sequence) {
            return $this->opd_sequence . '/' . substr($this->opd_year, -2);
        }
        return null;
    }



    /**
     * Get the patient's latest visit.
     */
    public function latestVisit(): HasOne
    {
        return $this->hasOne(Visit::class)->latestOfMany();
    }

    /**
     * Get the patient's age.
     */
    public function getAgeAttribute(): int
    {
        return $this->dob ? Carbon::parse($this->dob)->age : 0;
    }

    /**
     * Get the patient's age in years and months.
     */
    public function getAgeFormattedAttribute(): string
    {
        if (!$this->dob) {
            return 'N/A';
        }

        $dob = Carbon::parse($this->dob);
        $now = Carbon::now();

        $years = (int)$dob->diffInYears($now);
        $months = $dob->diffInMonths($now) % 12;

        if ($years == 0) {
            return "{$months} months";
        } elseif ($months == 0) {
            return "{$years} years";
        } else {
            return "{$years}y {$months}m";
        }
    }

    /**
     * Format the phone number using helper function.
     */
    public function getFormattedPhoneAttribute(): string
    {
        return $this->phone ? formatPhone($this->phone) : '';
    }

    /**
     * Scope a query to search patients.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('emrn', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('cnic', 'like', "%{$search}%");
    }

    /**
     * Scope a query to only include NHMP patients.
     */
    public function scopeNhmp($query)
    {
        return $query->where('is_nhmp', true);
    }

    /**
     * Scope a query to only include active patients.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include patients by office.
     */
    public function scopeByOffice($query, $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    /**
     * Scope a query to only include patients by designation.
     */
    public function scopeByDesignation($query, $designationId)
    {
        return $query->where('designation_id', $designationId);
    }

    /**
     * Get the designation associated with the patient.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Get the office associated with the patient.
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get the vitals for the patient.
     */
    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    /**
     * Get the latest vitals for the patient.
     */
    public function latestVitals()
    {
        return $this->hasOne(Vital::class)->latestOfMany();
    }

    /**
     * Get all vitals ordered by latest first.
     */
    public function orderedVitals()
    {
        return $this->hasMany(Vital::class)->latest();
    }

    /**
     * Get the full office hierarchy for NHMP patients.
     */
    public function getOfficeHierarchyAttribute()
    {
        if (!$this->is_nhmp || !$this->office) {
            return null;
        }

        $hierarchy = [];
        $currentOffice = $this->office;

        while ($currentOffice) {
            $hierarchy[] = [
                'id' => $currentOffice->id,
                'name' => $currentOffice->name,
                'type' => $currentOffice->type,
            ];
            $currentOffice = $currentOffice->parent;
        }

        return array_reverse($hierarchy);
    }

    /**
     * Get patient's designation name with fallback to rank.
     */
    public function getDesignationNameAttribute()
    {
        if ($this->designation) {
            return $this->designation->name;
        }

        return $this->rank ?? 'N/A';
    }

    /**
     * Get formatted designation name with BPS
     */
    public function getFormattedDesignationAttribute()
    {
        if (!$this->designation) {
            return null;
        }

        $formatted = $this->designation->title;

        if ($this->designation->bps) {
            $formatted .= ' (BPS-' . $this->designation->bps . ')';
        }

        if ($this->designation->short_form) {
            $formatted .= ' - ' . $this->designation->short_form;
        }

        return $formatted;
    }

    /**
     * Get full NHMP details
     */
    public function getNhmpDetailsAttribute()
    {
        if (!$this->is_nhmp) {
            return null;
        }

        return [
            'designation' => $this->formatted_designation,
            'office' => $this->office ? $this->office->name : null,
            'rank' => $this->rank,
            'cadre_type' => $this->designation ? $this->designation->cadre_type : null,
            'rank_group' => $this->designation ? $this->designation->rank_group : null,
        ];
    }

    /**
     * Get patient's office name with fallback.
     */
    public function getOfficeNameAttribute()
    {
        if ($this->office) {
            return $this->office->name;
        }

        return 'N/A';
    }

    /**
     * Check if patient has any active visits.
     */
    public function hasActiveVisits()
    {
        return $this->visits()->whereIn('status', ['waiting', 'in_progress'])->exists();
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }


    /**
     * Get all diagnoses for the patient.
     */
    public function diagnoses()
    {
        return $this->hasManyThrough(Diagnosis::class, Visit::class);
    }

    /**
     * Get patient statistics.
     */
    public function getStatisticsAttribute()
    {
        return [
            'total_visits' => $this->visits()->count(),
            'active_visits' => $this->visits()->whereIn('status', ['waiting', 'in_progress'])->count(),
            'completed_visits' => $this->visits()->where('status', 'completed')->count(),
            'total_prescriptions' => $this->prescriptions()->count(),
            'pending_prescriptions' => $this->prescriptions()->where('status', 'pending')->count(),
            'total_lab_reports' => $this->labReports()->count(),
            'pending_lab_reports' => $this->labReports()->where('status', 'pending')->count(),
        ];
    }

    /**
     * Get all prescriptions for the patient.
     */
    public function prescriptions()
    {
        return $this->hasManyThrough(Prescription::class, Visit::class);
    }

    /**
     * Get all lab reports for the patient.
     */
    public function labReports()
    {
        return $this->hasMany(LabOrder::class);
    }

    /**
     * Get the patient's formatted CNIC.
     */
    protected function formattedCnic(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->cnic ? formatCNIC($this->cnic) : null
        );
    }

    /**
     * Get the patient's NHMP status badge.
     */
    protected function nhmpBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_nhmp
                ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">NHMP</span>'
                : '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Non-NHMP</span>'
        );
    }

    /**
     * Get the patient's NHMP status text.
     */
    protected function nhmpStatus(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_nhmp ? 'NHMP Patient' : 'General Patient'
        );
    }
}
