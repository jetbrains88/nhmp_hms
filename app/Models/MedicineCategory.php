<?php

namespace App\Models;

use App\Models\Medicine;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineCategory extends Model
{
    use SoftDeletes, HasUUID, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'display_order',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];


    // Add this scope method
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Optional: Add an inactive scope too
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the medicines for the category.
     */
    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'category_id');
    }
}
