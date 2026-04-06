<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTestType extends Model
{
    use HasUUID, Auditable;

    protected $fillable = [
        'uuid',
        'name',
        'department',
        'sample_type'
    ];

    public function parameters(): HasMany
    {
        return $this->hasMany(LabTestParameter::class);
    }

    public function labOrderItems(): HasMany
    {
        return $this->hasMany(LabOrderItem::class, 'lab_test_type_id');
    }
}
