<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalSpecialty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function diagnoses()
    {
        return $this->belongsToMany(Diagnosis::class, 'diagnosis_medical_specialty')
                    ->withPivot('referral_notes')
                    ->withTimestamps();
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

