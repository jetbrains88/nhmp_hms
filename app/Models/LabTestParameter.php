<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTestParameter extends Model
{
    use HasUUID, Auditable;

    protected $fillable = [
        'uuid',
        'lab_test_type_id',
        'name',
        'group_name',
        'unit',
        'reference_range',
        'min_range',
        'max_range',
        'input_type',
        'order'
    ];

    protected $casts = [
        'min_range' => 'decimal:3',
        'max_range' => 'decimal:3',
        'order' => 'integer',
    ];

    public function labTestType(): BelongsTo
    {
        return $this->belongsTo(LabTestType::class);
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class);
    }

    public function isNumeric(): bool
    {
        return $this->input_type === 'number' || ($this->min_range !== null && $this->max_range !== null);
    }
}
