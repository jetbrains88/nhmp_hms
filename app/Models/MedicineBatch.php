<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineBatch extends Model
{
    use SoftDeletes, HasUUID, MultiTenant, Auditable;

    protected $fillable = [
        'uuid',
        'branch_id',
        'medicine_id',
        'batch_number',
        'rc_number',
        'expiry_date',
        'unit_price',
        'sale_price',
        'stock',
        'remaining_quantity',
        'is_active'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'remaining_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function prescriptionDispensations(): HasMany
    {
        return $this->hasMany(PrescriptionDispensation::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function getExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }
}
