<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\EmployeeDetail;
use App\Models\Patient;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{

    use HasUUID;
    protected $fillable = ['uuid', 'name', 'type', 'parent_id'];

    /**
     * Get the parent office.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'parent_id');
    }


    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function employeeDetails(): HasMany
    {
        return $this->hasMany(EmployeeDetail::class);
    }



    /**
     * Get all ancestors (recursive).
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Get the patients in this office (for NHMP patients).
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Get the NHMP patients in this office.
     */
    public function nhmpPatients()
    {
        return $this->hasMany(Patient::class)->where('is_nhmp', true);
    }

    /**
     * Scope for root offices (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the full hierarchical path.
     */
    public function getFullPathAttribute(): string
    {
        $path = [];
        $current = $this;

        while ($current) {
            $path[] = $current->name;
            $current = $current->parent;
        }

        return implode(' → ', array_reverse($path));
    }

    /**
     * Get the office hierarchy as an array.
     */
    public function getHierarchyAttribute(): array
    {
        $hierarchy = [];
        $current = $this;

        while ($current) {
            $hierarchy[] = [
                'id' => $current->id,
                'name' => $current->name,
                'type' => $current->type,
            ];
            $current = $current->parent;
        }

        return array_reverse($hierarchy);
    }

    /**
     * Get the office type badge.
     */
    protected function typeBadge(): Attribute
    {
        $badgeClasses = [
            'Region' => 'bg-blue-100 text-blue-800',
            'Zone' => 'bg-green-100 text-green-800',
            'Sector' => 'bg-yellow-100 text-yellow-800',
            'PLHQ' => 'bg-purple-100 text-purple-800',
            'Beat' => 'bg-gray-100 text-gray-800',
            'Office' => 'bg-indigo-100 text-indigo-800',
        ];

        $class = $badgeClasses[$this->type] ?? 'bg-gray-100 text-gray-800';

        return Attribute::make(
            get: fn() => '<span class="px-2 py-1 rounded text-xs ' . $class . '">' . $this->type . '</span>'
        );
    }

    /**
     * Check if this office has child offices.
     */
    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get all beats under this office (recursively for higher levels).
     */
    public function getAllBeatsAttribute()
    {
        if ($this->type === 'Beat') {
            return collect([$this]);
        }

        $beats = collect();

        // Recursively collect beats from children
        $collectBeats = function ($office) use (&$collectBeats, &$beats) {
            if ($office->type === 'Beat') {
                $beats->push($office);
            } else {
                foreach ($office->children as $child) {
                    $collectBeats($child);
                }
            }
        };

        $collectBeats($this);

        return $beats;
    }

    /**
     * Get all PLHQs under this office (recursively for higher levels).
     */
    public function getAllPlhqsAttribute()
    {
        if ($this->type === 'PLHQ') {
            return collect([$this]);
        }

        $plhqs = collect();

        // Recursively collect PLHQs from children
        $collectPlhqs = function ($office) use (&$collectPlhqs, &$plhqs) {
            if ($office->type === 'PLHQ') {
                $plhqs->push($office);
            } else {
                foreach ($office->children as $child) {
                    $collectPlhqs($child);
                }
            }
        };

        $collectPlhqs($this);

        return $plhqs;
    }

    /**
     * Get office statistics.
     */
    public function getStatisticsAttribute(): array
    {
        return [
            'total_patients' => $this->patients()->count(),
            'nhmp_patients' => $this->nhmpPatients()->count(),
            'child_offices' => $this->children()->count(),
            'all_beats_count' => $this->getAllBeatsAttribute()->count(),
            'all_plhqs_count' => $this->getAllPlhqsAttribute()->count(),
        ];
    }

    /**
     * Scope a query to search offices.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%");
    }

    /**
     * Get offices by parent with eager loading.
     */
    public static function getHierarchical($parentId = null)
    {
        return self::where('parent_id', $parentId)
            ->with(['children' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all offices as a flat list with hierarchy indicator.
     */
    public static function getFlatList(): array
    {
        $offices = self::with('parent')->orderBy('name')->get();
        $list = [];

        foreach ($offices as $office) {
            $prefix = str_repeat('— ', $office->getLevel());
            $list[$office->id] = $prefix . $office->name . ' (' . $office->type . ')';
        }

        return $list;
    }

    /**
     * Get the level of the office in hierarchy.
     */
    public function getLevel(): int
    {
        $level = 0;
        $current = $this->parent;

        while ($current) {
            $level++;
            $current = $current->parent;
        }

        return $level;
    }

    /**
     * Get office types count.
     */
    public static function getTypesCount(): array
    {
        return self::select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Check if office is a descendant of another office.
     */
    public function isDescendantOf($officeId): bool
    {
        $current = $this->parent;

        while ($current) {
            if ($current->id == $officeId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }

    /**
     * Get office tree for dropdowns.
     */
    public static function getTree($parentId = null, $level = 0): array
    {
        $offices = self::where('parent_id', $parentId)
            ->orderBy('name')
            ->get();

        $tree = [];

        foreach ($offices as $office) {
            $prefix = str_repeat('— ', $level);
            $tree[] = [
                'id' => $office->id,
                'name' => $prefix . $office->name . ' (' . $office->type . ')',
                'type' => $office->type,
                'level' => $level,
            ];

            $tree = array_merge($tree, self::getTree($office->id, $level + 1));
        }

        return $tree;
    }
}
