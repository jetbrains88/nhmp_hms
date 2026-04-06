<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryLog extends Model
{
    use HasUUID, MultiTenant;

    protected $fillable = [ç
        'uuid',
        'branch_id',
        'medicine_id',
        'user_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference_id',
        'reference_type',
        'prescription_dispensation_id',
        'notes',
        'rc_number',
        'medicine_batch_id'
    ];
    protected $table = 'inventory_logs';
    protected $casts = [
        'type' => 'string',
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicineBatch(): BelongsTo
    {
        return $this->belongsTo(MedicineBatch::class);
    }

    public function prescriptionDispensation(): BelongsTo
    {
        return $this->belongsTo(PrescriptionDispensation::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
