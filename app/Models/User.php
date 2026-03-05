<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Diagnosis;
use App\Models\InventoryLog;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\Notification;
use App\Models\Prescription;
use App\Models\Role;
use App\Models\StockAlert;
use App\Models\Visit;
use App\Models\Vital;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasUUID, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'primary_branch_id',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'preferences' => 'array',
    ];

    protected $appends = ['current_branch_id'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the branches that this user belongs to.
     * Note: The pivot table doesn't have timestamps
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')
            ->withPivot('is_primary');
        // Don't use withTimestamps() or withoutTimestamps() - just leave it as is
    }

    /**
     * Get the primary branch of the user.
     */
    public function primaryBranch()
    {
        return $this->belongsTo(Branch::class, 'primary_branch_id');
    }

    /**
     * Get the roles assigned to this user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps(); // Keep timestamps if role_user has them
    }

    /**
     * Get all permissions of the user through roles.
     */
    public function permissions()
    {
        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole($roles)
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles($roles)
    {
        return $this->roles->pluck('name')->intersect($roles)->count() === count($roles);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()->contains('name', $permissionName);
    }

    /**
     * Get the visits where this user is the doctor.
     */
    public function doctorVisits()
    {
        return $this->hasMany(Visit::class, 'doctor_id');
    }

    /**
     * Get the vitals recorded by this user.
     */
    public function recordedVitals()
    {
        return $this->hasMany(Vital::class, 'recorded_by');
    }

    /**
     * Get the diagnoses made by this user.
     */
    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class, 'doctor_id');
    }

    /**
     * Get the prescriptions prescribed by this user.
     */
    public function prescribedPrescriptions()
    {
        return $this->hasMany(Prescription::class, 'prescribed_by');
    }

    /**
     * Get the prescriptions dispensed by this user.
     */
    public function dispensedPrescriptions()
    {
        return $this->hasMany(Prescription::class, 'dispensed_by');
    }

    /**
     * Get the lab orders requested by this user.
     */
    public function requestedLabOrders()
    {
        return $this->hasMany(LabOrder::class, 'doctor_id');
    }

    /**
     * Get the lab orders processed by this user.
     */
    public function processedLabOrders()
    {
        return $this->hasMany(LabOrder::class, 'technician_id');
    }

    /**
     * Get the lab orders verified by this user.
     */
    public function verifiedLabOrders()
    {
        return $this->hasMany(LabOrder::class, 'verified_by_user_id');
    }

    /**
     * Get the inventory logs created by this user.
     */
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class, 'user_id');
    }

    /**
     * Get the stock alerts resolved by this user.
     */
    public function resolvedStockAlerts()
    {
        return $this->hasMany(StockAlert::class, 'resolved_by');
    }

    /**
     * Get the audit logs for this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get the notifications triggered by this user.
     */
    public function triggeredNotifications()
    {
        return $this->hasMany(Notification::class, 'triggered_by');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Scope a query to only include users assigned to a specific branch.
     */
    public function scopeInBranch($query, $branchId)
    {
        return $query->whereHas('branches', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }

    /**
     * Get the user's role names as a comma-separated string.
     */
    public function getRoleNamesAttribute(): string
    {
        return $this->roles->pluck('display_name')->implode(', ');
    }

    /**
     * Get the user's role badges for display.
     */
    public function getRoleBadgesAttribute(): string
    {
        $badges = '';
        foreach ($this->roles as $role) {
            $color = match ($role->name) {
                'super_admin' => 'bg-purple-100 text-purple-800',
                'admin' => 'bg-red-100 text-red-800',
                'doctor' => 'bg-green-100 text-green-800',
                'pharmacy' => 'bg-blue-100 text-blue-800',
                'lab' => 'bg-yellow-100 text-yellow-800',
                'nurse' => 'bg-pink-100 text-pink-800',
                'reception' => 'bg-indigo-100 text-indigo-800',
                default => 'bg-gray-100 text-gray-800',
            };
            $badges .= '<span class="px-2 py-1 text-xs rounded-full ' . $color . ' mr-1">' . $role->display_name . '</span>';
        }
        return $badges;
    }

    /**
     * Check if user can access a specific branch.
     */
    public function canAccessBranch($branchId): bool
    {
        return $this->branches()->where('branch_id', $branchId)->exists();
    }

    /**
     * Get the user's accessible branch IDs.
     */
    public function getAccessibleBranchIdsAttribute(): array
    {
        return $this->branches->pluck('id')->toArray();
    }

    /**
     * Get the user's display name with roles.
     */
    public function getDisplayNameAttribute(): string
    {
        $role = $this->roles->first();
        $rolePrefix = $role ? '[' . $role->display_name . '] ' : '';
        return $rolePrefix . $this->name;
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is doctor.
     */
    public function isDoctor(): bool
    {
        return $this->hasRole('doctor');
    }

    /**
     * Check if user is pharmacy staff.
     */
    public function isPharmacy(): bool
    {
        return $this->hasRole('pharmacy');
    }

    /**
     * Check if user is lab staff.
     */
    public function isLab(): bool
    {
        return $this->hasRole('lab');
    }

    /**
     * Check if user is nurse.
     */
    public function isNurse(): bool
    {
        return $this->hasRole('nurse');
    }

    /**
     * Check if user is receptionist.
     */
    public function isReception(): bool
    {
        return $this->hasRole('reception');
    }

    /**
     * Get the current branch ID for the user.
     */
    public function getCurrentBranchIdAttribute()
    {
        // First check session
        if (session()->has('current_branch_id')) {
            return session('current_branch_id');
        }

        // Then try primary branch relationship safely
        try {
            $primaryBranch = $this->primaryBranch;
            if ($primaryBranch) {
                return $primaryBranch->id;
            }
        } catch (\Exception $e) {
            // Log the error but don't crash
            \Log::warning('Error accessing primary branch', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }

        // Fallback to first available branch
        try {
            $firstBranch = $this->branches()->first();
            if ($firstBranch) {
                return $firstBranch->id;
            }
        } catch (\Exception $e) {
            \Log::warning('Error accessing branches', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }


    public function prescribedDiagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'doctor_id');
    }


    public function labOrdersAsDoctor(): HasMany
    {
        return $this->hasMany(LabOrder::class, 'doctor_id');
    }

    public function labOrdersAsTechnician(): HasMany
    {
        return $this->hasMany(LabOrderItem::class, 'technician_id');
    }

    public function appointmentsAsDoctor(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function receivedNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
    // Add helper method
    public function getPreference($key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }
}
