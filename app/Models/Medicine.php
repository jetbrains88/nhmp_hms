<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\InventoryLog;
use App\Models\MedicineBatch;
use App\Models\MedicineCategory;
use App\Models\MedicineForm;
use App\Models\Prescription;
use App\Models\StockAlert;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use SoftDeletes, HasFactory, HasUUID, MultiTenant, Auditable;

    protected $fillable = [
        'uuid',
        'branch_id',
        'is_global',
        'name',
        'generic_name',
        'brand',
        'manufacturer',
        'form_id',
        'strength_value',
        'strength_unit',
        'unit',
        'category_id',
        'description',
        'reorder_level',
        'is_active',
        'requires_prescription'
    ];

    protected $appends = ['stock'];

    protected $casts = [
        'is_global' => 'boolean',
        'is_active' => 'boolean',
        'requires_prescription' => 'boolean',
        'strength_value' => 'decimal:2',
        'reorder_level' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(MedicineForm::class, 'form_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(MedicineBatch::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->batches()->sum('remaining_quantity');
    }

    public function getLowStockAttribute(): bool
    {
        return $this->total_stock <= $this->reorder_level;
    }

    /**
     * Check if any batch of this medicine is about to expire
     */
    public function isAboutToExpire(): bool
    {
        foreach ($this->batches as $batch) {
            if ($batch->expiry_date) {
                $expiryDate = Carbon::parse($batch->expiry_date);
                $daysUntilExpiry = now()->diffInDays($expiryDate, false);

                if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get the earliest expiry date from all batches
     */
    public function getEarliestExpiryDateAttribute()
    {
        $earliest = $this->batches()
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->first();

        return $earliest ? $earliest->expiry_date : null;
    }

    /**
     * Scope a query to only include active medicines.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getStockAttribute(): int
    {
        return $this->total_stock;
    }

    public function getStrengthAttribute(): string
    {
        return $this->strength_value . ($this->strength_unit ? ' ' . $this->strength_unit : '');
    }
}
