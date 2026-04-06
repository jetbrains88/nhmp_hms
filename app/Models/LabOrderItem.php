<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabOrderItem extends Model
{
    use HasUUID, Auditable;

    protected $fillable = [
        'uuid',
        'lab_order_id',
        'lab_test_type_id',
        'technician_id',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function labOrder(): BelongsTo
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function labTestType(): BelongsTo
    {
        return $this->belongsTo(LabTestType::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class, 'lab_order_item_id');
    }

    public function labSampleInfos(): HasMany
    {
        return $this->hasMany(LabSampleInfo::class, 'lab_order_item_id');
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getResultCountAttribute(): int
    {
        return $this->labResults()->count();
    }

    public function getExpectedParameterCountAttribute(): int
    {
        return $this->labTestType->parameters()->count();
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->result_count >= $this->expected_parameter_count;
    }

    public function sampleInfo()
    {
        return $this->hasOne(LabSampleInfo::class, 'lab_order_item_id');
    }
}
