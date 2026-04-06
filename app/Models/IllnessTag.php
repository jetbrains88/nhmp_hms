<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IllnessTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'icd_code',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ──────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────

    /**
     * Diagnoses that have this illness tag.
     */
    public function diagnoses()
    {
        return $this->belongsToMany(Diagnosis::class, 'diagnosis_illness_tag');
    }

    // ──────────────────────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Returns a color class based on category for badge styling.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'chronic'    => 'bg-red-100 text-red-800 border-red-200',
            'acute'      => 'bg-amber-100 text-amber-800 border-amber-200',
            'infectious' => 'bg-orange-100 text-orange-800 border-orange-200',
            default      => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Returns a Font Awesome icon class based on category.
     */
    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'chronic'    => 'fas fa-heartbeat',
            'acute'      => 'fas fa-bolt',
            'infectious' => 'fas fa-virus',
            default      => 'fas fa-tag',
        };
    }
}
