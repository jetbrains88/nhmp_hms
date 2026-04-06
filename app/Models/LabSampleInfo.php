<?php
// app/Models/LabSampleInfo.php

namespace App\Models;

use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabSampleInfo extends Model
{
    use HasUUID, Auditable;

    protected $fillable = [
        'uuid',
        'lab_order_item_id',
        'sample_collected_at',
        'sample_id',
        'sample_container',
        'sample_quantity',
        'sample_quantity_unit',
        'sample_condition',
        'special_instructions'
    ];

    protected $casts = [
        'sample_collected_at' => 'datetime',
        'sample_quantity' => 'decimal:2',
    ];

    public function labOrderItem(): BelongsTo
    {
        return $this->belongsTo(LabOrderItem::class);
    }

    /**
     * Get the lab order through the item.
     */
    public function labOrder()
    {
        return $this->hasOneThrough(
            LabOrder::class,
            LabOrderItem::class,
            'id', // Foreign key on lab_order_items table
            'id', // Foreign key on lab_orders table
            'lab_order_item_id', // Local key on lab_sample_infos table
            'lab_order_id' // Local key on lab_order_items table
        );
    }
}
