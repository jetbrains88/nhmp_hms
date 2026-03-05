<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Permission;
use App\Models\User;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
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
        'display_name',
        'level',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
    ];




    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the users that have this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps(); // Keep timestamps if role_user has them
    }

    /**
     * Get the permissions assigned to this role.
     * Note: The pivot table doesn't have timestamps
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
        // Don't use withTimestamps() - the pivot table doesn't have timestamps
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions->contains('name', $permissionName);
    }

    /**
     * Get the role's level badge.
     */
    public function getLevelBadgeAttribute(): string
    {
        return match (true) {
            $this->level >= 100 => 'bg-purple-100 text-purple-800',
            $this->level >= 80 => 'bg-red-100 text-red-800',
            $this->level >= 70 => 'bg-orange-100 text-orange-800',
            $this->level >= 60 => 'bg-yellow-100 text-yellow-800',
            $this->level >= 50 => 'bg-green-100 text-green-800',
            $this->level >= 40 => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the role's level description.
     */
    public function getLevelDescriptionAttribute(): string
    {
        return match (true) {
            $this->level >= 100 => 'System Level',
            $this->level >= 80 => 'Hospital Level',
            $this->level >= 70 => 'Department Head',
            $this->level >= 60 => 'Branch Admin',
            $this->level >= 50 => 'Staff',
            $this->level >= 40 => 'Assistant',
            default => 'Basic',
        };
    }
}
