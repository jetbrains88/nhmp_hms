<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalSpecialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'medical_specialty_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function medicalSpecialty()
    {
        return $this->belongsTo(MedicalSpecialty::class, 'medical_specialty_id');
    }

    public function diagnoses()
    {
        return $this->belongsToMany(
            Diagnosis::class,
            'diagnosis_external_specialist'
        )->withPivot('referral_notes')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhereNull('branch_id');
        });
    }

    public function getFullLabelAttribute(): string
    {
        $spec = $this->medicalSpecialty ? $this->medicalSpecialty->name : 'General Specialist';
        return "{$this->name} — {$spec}";
    }
}

