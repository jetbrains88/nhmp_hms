<?php

namespace App\Models;

use App\Models\Role;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;


class Permission extends Model
{
    use HasUUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'group',
        'display_name',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     * Note: The pivot table doesn't have timestamps
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
        // Don't use withTimestamps() - the pivot table doesn't have timestamps
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get all permission groups.
     */
    public static function getGroups(): array
    {
        return self::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();
    }
}
