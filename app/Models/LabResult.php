<?php

namespace App\Models;

use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabTestParameter;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    use HasUUID, Auditable;

    protected $table = 'lab_results';

    protected $fillable = [
        'uuid',
        'lab_order_item_id',
        'lab_test_parameter_id',
        'value_type',
        'numeric_value',
        'text_value',
        'boolean_value',
        'is_abnormal',
        'remarks'
    ];

    protected $casts = [
        'is_abnormal' => 'boolean',
        'numeric_value' => 'decimal:4',
        'boolean_value' => 'boolean',
        'value_type' => 'string',
    ];


    public function labOrderItem(): BelongsTo
    {
        return $this->belongsTo(LabOrderItem::class);
    }

    public function labTestParameter(): BelongsTo
    {
        return $this->belongsTo(LabTestParameter::class);
    }

    public function getDisplayValueAttribute(): string
    {
        switch ($this->value_type) {
            case 'numeric':
                return (string) $this->numeric_value;
            case 'boolean':
                return $this->boolean_value ? 'Positive' : 'Negative';
            default:
                return $this->text_value ?? '';
        }
    }
    /**
     * Get the order that owns the result.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(LabOrder::class, 'lab_order_id');
    }

    /**
     * Get the parameter that owns the result.
     */
    public function parameter(): BelongsTo
    {
        return $this->belongsTo(LabTestParameter::class, 'lab_test_parameter_id');
    }
}
