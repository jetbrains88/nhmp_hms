<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionAbbreviation extends Model
{
    use HasFactory;

    protected $fillable = [
        'abbreviation',
        'full_meaning',
        'category',
        'doses_per_day',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'doses_per_day' => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────

    /**
     * Prescriptions that use this abbreviation.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'abbreviation_id');
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
     * Returns a color class badge per category.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'frequency' => 'bg-blue-100 text-blue-800 border-blue-200',
            'route'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'timing'    => 'bg-purple-100 text-purple-800 border-purple-200',
            'dosage'    => 'bg-amber-100 text-amber-800 border-amber-200',
            default     => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Returns "ABBR — Full Meaning" label for dropdowns.
     */
    public function getDropdownLabelAttribute(): string
    {
        return "{$this->abbreviation} — {$this->full_meaning}";
    }

    /**
     * Grouped list for select dropdowns, keyed by category.
     */
    public static function groupedForSelect(): array
    {
        return static::active()
            ->orderBy('category')
            ->orderBy('abbreviation')
            ->get()
            ->groupBy('category')
            ->map(fn ($group) => $group->map(fn ($a) => [
                'id'          => $a->id,
                'abbr'        => $a->abbreviation,
                'label'       => $a->dropdown_label,
                'doses_per_day' => $a->doses_per_day,
            ]))
            ->toArray();
    }
}
