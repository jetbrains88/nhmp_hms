<?php

namespace App\Models;

use App\Models\EmployeeDetail;
use App\Models\Patient;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    use HasUUID;
    protected $fillable = [
        'uuid',
        'title',
        'short_form',
        'bps',
        'cadre_type',
        'rank_group',
    ];

    protected $casts = [
        'bps' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'full_title',
        'title_with_bps',
        'is_uniform',
        'is_gazetted',
        'seniority_level',
    ];

    public function employeeDetails(): HasMany
    {
        return $this->hasMany(EmployeeDetail::class);
    }

    /**
     * Get the full title with BPS and short form
     */
    protected function getFullTitleAttribute(): string
    {
        $title = $this->title;

        if ($this->bps) {
            $title .= " (BPS-{$this->bps})";
        }

        if ($this->short_form) {
            $title .= " [{$this->short_form}]";
        }

        return $title;
    }

    /**
     * Get title with BPS only
     */
    protected function getTitleWithBpsAttribute(): string
    {
        if ($this->bps) {
            return "{$this->title} (BPS-{$this->bps})";
        }

        return $this->title;
    }

    /**
     * Check if designation is uniform
     */
    protected function getIsUniformAttribute(): bool
    {
        return $this->cadre_type === 'uniform';
    }

    /**
     * Check if designation is gazetted officer
     */
    protected function getIsGazettedAttribute(): bool
    {
        // BPS 17 and above are typically gazetted officers
        return $this->bps >= 17;
    }

    /**
     * Get seniority level based on BPS
     */
    protected function getSeniorityLevelAttribute(): string
    {
        if (!$this->bps) {
            return 'unknown';
        }

        if ($this->bps >= 20) {
            return 'senior_management';
        } elseif ($this->bps >= 17) {
            return 'middle_management';
        } elseif ($this->bps >= 14) {
            return 'junior_management';
        } elseif ($this->bps >= 11) {
            return 'senior_staff';
        } else {
            return 'staff';
        }
    }

    /**
     * Get seniority level label
     */
    public function getSeniorityLevelLabelAttribute(): string
    {
        $levels = [
            'senior_management' => 'Senior Management',
            'middle_management' => 'Middle Management',
            'junior_management' => 'Junior Management',
            'senior_staff' => 'Senior Staff',
            'staff' => 'Staff',
            'unknown' => 'Not Specified',
        ];

        return $levels[$this->seniority_level] ?? 'Unknown';
    }

    /**
     * Get the badge color for seniority level
     */
    public function getSeniorityBadgeAttribute(): string
    {
        $colors = [
            'senior_management' => 'bg-purple-100 text-purple-800',
            'middle_management' => 'bg-blue-100 text-blue-800',
            'junior_management' => 'bg-green-100 text-green-800',
            'senior_staff' => 'bg-yellow-100 text-yellow-800',
            'staff' => 'bg-gray-100 text-gray-800',
            'unknown' => 'bg-gray-100 text-gray-800',
        ];

        $color = $colors[$this->seniority_level] ?? 'bg-gray-100 text-gray-800';

        return "<span class='px-2 py-1 rounded text-xs font-medium {$color}'>
            {$this->seniority_level_label}
        </span>";
    }

    /**
     * Get cadre type label
     */
    public function getCadreTypeLabelAttribute(): string
    {
        if (!$this->cadre_type) {
            return 'Not Specified';
        }

        return match ($this->cadre_type) {
            'uniform' => 'Uniform Cadre',
            'non_uniform' => 'Non-Uniform Cadre',
            'technical' => 'Technical Cadre',
            'administrative' => 'Administrative Cadre',
            'medical' => 'Medical Cadre',
            default => ucfirst(str_replace('_', ' ', $this->cadre_type)),
        };
    }

    /**
     * Get cadre type badge
     */
    public function getCadreTypeBadgeAttribute(): string
    {
        $colors = [
            'uniform' => 'bg-red-100 text-red-800',
            'non_uniform' => 'bg-blue-100 text-blue-800',
            'technical' => 'bg-green-100 text-green-800',
            'administrative' => 'bg-purple-100 text-purple-800',
            'medical' => 'bg-teal-100 text-teal-800',
        ];

        $color = $colors[$this->cadre_type] ?? 'bg-gray-100 text-gray-800';

        return "<span class='px-2 py-1 rounded text-xs font-medium {$color}'>
            {$this->cadre_type_label}
        </span>";
    }

    /**
     * Scope a query to filter by cadre type
     */
    public function scopeByCadreType($query, $cadreType)
    {
        return $query->where('cadre_type', $cadreType);
    }

    /**
     * Scope a query to filter by BPS range
     */
    public function scopeByBpsRange($query, $min, $max = null)
    {
        if ($max) {
            return $query->whereBetween('bps', [$min, $max]);
        }

        return $query->where('bps', '>=', $min);
    }

    /**
     * Scope a query to get gazetted officers (BPS 17+)
     */
    public function scopeGazetted($query)
    {
        return $query->where('bps', '>=', 17);
    }

    /**
     * Scope a query to get non-gazetted officers
     */
    public function scopeNonGazetted($query)
    {
        return $query->where('bps', '<', 17)->orWhereNull('bps');
    }

    /**
     * Scope a query to order by seniority (higher BPS first)
     */
    public function scopeBySeniority($query)
    {
        return $query->orderByRaw('bps DESC NULLS LAST');
    }

    /**
     * Scope a query to search designations
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('short_form', 'like', "%{$search}%")
            ->orWhere('rank_group', 'like', "%{$search}%");
    }

    /**
     * Get the patients with this designation
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Count active patients with this designation
     */
    public function getActivePatientsCountAttribute(): int
    {
        return $this->patients()->where('is_active', true)->count();
    }

    /**
     * Get statistics for this designation
     */
    public function getStatisticsAttribute(): array
    {
        $totalPatients = $this->patients()->count();
        $activePatients = $this->patients()->where('is_active', true)->count();
        $nhmpPatients = $this->patients()->where('is_nhmp', true)->count();

        return [
            'total_patients' => $totalPatients,
            'active_patients' => $activePatients,
            'inactive_patients' => $totalPatients - $activePatients,
            'nhmp_patients' => $nhmpPatients,
            'percentage_active' => $totalPatients > 0 ? round(($activePatients / $totalPatients) * 100, 1) : 0,
        ];
    }

    /**
     * Get similar designations in the same rank group
     */
    public function similarDesignations()
    {
        if (!$this->rank_group) {
            return collect();
        }

        return Designation::where('rank_group', $this->rank_group)
            ->where('id', '!=', $this->id)
            ->get();
    }

    /**
     * Get next higher designation (by BPS)
     */
    public function getNextHigherDesignationAttribute()
    {
        return Designation::where('bps', '>', $this->bps)
            ->orderBy('bps')
            ->first();
    }

    /**
     * Get next lower designation (by BPS)
     */
    public function getNextLowerDesignationAttribute()
    {
        return Designation::where('bps', '<', $this->bps)
            ->orderByDesc('bps')
            ->first();
    }

    /**
     * Get hierarchy level based on BPS
     */
    public function getHierarchyLevelAttribute(): int
    {
        if (!$this->bps) {
            return 0;
        }

        // Higher BPS = higher hierarchy level
        return $this->bps;
    }

    /**
     * Check if this designation can be assigned to patients
     */
    public function canBeAssigned(): bool
    {
        // Add any business rules here
        return true;
    }

    /**
     * Get suggested office types for this designation
     */
    public function getSuggestedOfficeTypesAttribute(): array
    {
        // Based on cadre type, suggest appropriate office types
        return match ($this->cadre_type) {
            'uniform' => ['police_station', 'security_office', 'headquarters'],
            'medical' => ['hospital', 'clinic', 'health_center'],
            'technical' => ['technical_division', 'lab', 'workshop'],
            'administrative' => ['office', 'secretariat', 'admin_office'],
            default => ['office', 'headquarters'],
        };
    }

    /**
     * Format BPS with label
     */
    public function getFormattedBpsAttribute(): string
    {
        if (!$this->bps) {
            return 'Not Specified';
        }

        return "BPS-{$this->bps}";
    }

    /**
     * Get display name for dropdowns
     */
    public function getDropdownDisplayAttribute(): string
    {
        $display = $this->title;

        if ($this->short_form) {
            $display .= " ({$this->short_form})";
        }

        if ($this->bps) {
            $display .= " - BPS-{$this->bps}";
        }

        return $display;
    }

    /**
     * Get short display (for compact views)
     */
    public function getShortDisplayAttribute(): string
    {
        if ($this->short_form) {
            return $this->short_form;
        }

        return $this->title;
    }

    /**
     * Validate BPS range
     */
    public static function validateBps($bps): bool
    {
        // BPS typically ranges from 1 to 22 in Pakistan
        return $bps >= 1 && $bps <= 22;
    }

    /**
     * Get all available BPS scales
     */
    public static function getAvailableBpsScales(): array
    {
        return array_map(function ($bps) {
            return [
                'value' => $bps,
                'label' => "BPS-{$bps}",
                'category' => match (true) {
                    $bps >= 17 => 'Gazetted Officer',
                    $bps >= 11 => 'Non-Gazetted Officer',
                    default => 'Staff',
                }
            ];
        }, range(1, 22));
    }

    /**
     * Get all cadre types
     */
    public static function getCadreTypes(): array
    {
        return [
            'uniform' => 'Uniform Cadre',
            'non_uniform' => 'Non-Uniform Cadre',
            'technical' => 'Technical Cadre',
            'administrative' => 'Administrative Cadre',
            'medical' => 'Medical Cadre',
            'education' => 'Education Cadre',
            'legal' => 'Legal Cadre',
            'finance' => 'Finance Cadre',
            'other' => 'Other',
        ];
    }
}
