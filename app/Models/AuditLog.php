<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditLog extends Model
{
    use HasUUID, MultiTenant;

    protected $fillable = [
        'uuid',
        'user_id',
        'branch_id',
        'action',
        'entity_type',
        'entity_id',
        'ip_address'
    ];

    protected $casts = [
        'action' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(AuditLogDetail::class);
    }
}
