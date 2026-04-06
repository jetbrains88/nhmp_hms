<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionDispensation extends Model
{
    use HasUUID, Auditable;

    protected $fillable = [
        'uuid',
        'prescription_id',
        'quantity_dispensed',
        'dispensed_by',
        'dispensed_at',
        'medicine_batch_id',
        'alternative_medicine_id',
        'notes'
    ];

    protected $casts = [
        'dispensed_at'      => 'datetime',
        'quantity_dispensed'=> 'integer',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function medicineBatch(): BelongsTo
    {
        return $this->belongsTo(MedicineBatch::class);
    }

    /**
     * The alternative medicine dispensed (same generic name, different brand).
     */
    public function alternativeMedicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'alternative_medicine_id');
    }

    /**
     * Returns true when an alternative medicine was substituted.
     */
    public function getIsAlternativeAttribute(): bool
    {
        return !is_null($this->alternative_medicine_id);
    }

    public function inventoryLogs()
    {
        return $this->morphMany(InventoryLog::class, 'reference');
    }
}
