<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'tax_id',
        'payment_terms',
        'lead_time_days',
        'rating',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lead_time_days' => 'integer',
        'rating' => 'decimal:1',
    ];

    /**
     * Get the medicines supplied by this supplier.
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }
}
