<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use App\Models\LabSampleInfo;
use App\Models\LabTestType;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Traits\Auditable;
use App\Traits\HasUUID;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabOrder extends Model
{
    use HasUUID, MultiTenant, Auditable;

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Priority constants
     */
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_URGENT = 'urgent';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'branch_id',
        'patient_id',
        'visit_id',
        'doctor_id',
        'lab_test_type_id',
        'collection_date',
        'reporting_date',
        'lab_number',
        'priority',
        'status',
        'is_verified',
        'verified_by_user_id',
        'verified_at',
        'device_name',
        'comments'
    ];

    protected $casts = [
        'collection_date' => 'datetime',
        'reporting_date' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'priority' => 'string',
        'status' => 'string',
    ];


    public function items(): HasMany
    {
        return $this->hasMany(LabOrderItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public function labTestType()
    {
        return $this->belongsTo(LabTestType::class);
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(LabTestType::class, 'lab_test_type_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get the patient associated with the lab order.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }


    /**
     * Get the visit associated with the lab order.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the doctor who ordered the test.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the technician who processed the test.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Get the results for this order through items.
     */
    public function results()
    {
        return $this->hasManyThrough(
            LabResult::class,
            LabOrderItem::class,
            'lab_order_id', // Foreign key on lab_order_items table
            'lab_order_item_id', // Foreign key on lab_results table
            'id', // Local key on lab_orders table
            'id' // Local key on lab_order_items table
        );
    }

    /**
     * Get formatted results for display - FIXED to use correct relationship
     */
    public function getFormattedResultsAttribute()
    {
        $results = [];

        foreach ($this->items as $item) {
            foreach ($item->labResults as $result) {
                $parameter = $result->labTestParameter;
                $results[] = [
                    'test' => $parameter?->name ?? 'Unknown',
                    'result' => $result->display_value,
                    'normal_range' => preg_replace('/\\\\n/', "\n", $parameter?->reference_range ?? ''),
                    'units' => $parameter?->unit,
                    'is_abnormal' => $result->is_abnormal,
                    'group_name' => $parameter?->group_name,
                    'parameter_id' => $parameter?->id,
                ];
            }
        }

        return $results;
    }

    /**
     * Check if the order has any results - FIXED to use correct relationship
     */
    public function hasResults(): bool
    {
        foreach ($this->items as $item) {
            if ($item->relationLoaded('labResults')) {
                if ($item->labResults->count() > 0) {
                    return true;
                }
            } else {
                if ($item->labResults()->count() > 0) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Get the sample info for this order through items.
     * Since sample info is linked to order items, we need to access it through items
     */
    public function sampleInfos()
    {
        return $this->hasManyThrough(
            LabSampleInfo::class,
            LabOrderItem::class,
            'lab_order_id', // Foreign key on lab_order_items table
            'lab_order_item_id', // Foreign key on lab_sample_infos table
            'id', // Local key on lab_orders table
            'id' // Local key on lab_order_items table
        );
    }

    /**
     * Get the first sample info for this order (for backward compatibility)
     */
    public function getSampleInfoAttribute()
    {
        return $this->sampleInfos()->first();
    }

    /**
     * Generate a lab number.
     * Uses atomic lock to prevent duplicates.
     */
    public static function generateLabNumber(): string
    {
        return DB::transaction(function () {
            $date = now()->format('Ymd');

            $lastOrder = self::whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->latest()
                ->first();

            $sequence = 1;
            if ($lastOrder && $lastOrder->lab_number) {
                $parts = explode('-', $lastOrder->lab_number);
                if (count($parts) === 3 && is_numeric($parts[2])) {
                    $sequence = intval($parts[2]) + 1;
                } else {
                    $sequence = self::whereDate('created_at', now()->toDateString())->count() + 1;
                }
            }

            return "LAB-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate a sample ID.
     */
    public static function generateSampleId(): string
    {
        $date = now();
        $year = $date->format('y');
        $month = $date->format('m');
        $day = $date->format('d');
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return "SMP-{$year}{$month}{$day}-{$random}";
    }

    // ============ SCOPES ============

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query by priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query for urgent orders.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_URGENT); // REMOVED emergency
    }

    /**
     * Scope a query for unverified completed orders.
     */
    public function scopeUnverified($query)
    {
        return $query->where('status', self::STATUS_COMPLETED)
            ->where('is_verified', false);
    }

    /**
     * Scope a query for overdue orders.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED])
            ->where('created_at', '<', now()->subHours(24));
    }

    // ============ STATUS CHECKS ============

    /**
     * Check if order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if order is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if order is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    /**
     * Check if order is urgent.
     */
    /**
     * Check if order is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->priority === self::PRIORITY_URGENT; // REMOVED emergency check
    }

    // ============ RESULT METHODS ============

    /**
     * Check if order has abnormal results.
     */
    public function hasAbnormalResults(): bool
    {
        return $this->results()->where('is_abnormal', true)->exists();
    }



    /**
     * Check if order has sample info.
     */
    public function hasSampleInfo(): bool
    {
        return $this->sampleInfo()->exists();
    }

    // ============ ACTION METHODS ============

    /**
     * Mark order as completed.
     */
    public function markAsCompleted(?User $technician = null): bool
    {
        $data = [
            'status' => self::STATUS_COMPLETED,
            'reporting_date' => now(),
        ];

        if ($technician) {
            $data['technician_id'] = $technician->id;
        }

        return $this->update($data);
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(?User $technician = null): bool
    {
        $data = [
            'status' => self::STATUS_PROCESSING,
        ];

        if ($technician) {
            $data['technician_id'] = $technician->id;
        }

        if (empty($this->collection_date)) {
            $data['collection_date'] = now();
        }

        return $this->update($data);
    }

    /**
     * Mark order as cancelled.
     */
    public function markAsCancelled(string $reason = null): bool
    {
        $data = [
            'status' => self::STATUS_CANCELLED,
        ];

        if ($reason) {
            $data['comments'] = $this->comments
                ? $this->comments . ' | Cancelled: ' . $reason
                : 'Cancelled: ' . $reason;
        }

        return $this->update($data);
    }

    /**
     * Verify the report.
     */
    public function verify(User $verifier, ?string $notes = null): bool
    {
        $data = [
            'is_verified' => true,
            'verified_by_user_id' => $verifier->id,
            'verified_at' => now(),
        ];

        if ($notes) {
            $data['comments'] = $this->comments
                ? $this->comments . ' | Verified: ' . $notes
                : 'Verified: ' . $notes;
        }

        return $this->update($data);
    }

    // ============ ATTRIBUTE HELPERS ============

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the priority badge class.
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the turnaround time in hours.
     */
    public function getTurnaroundTimeAttribute(): ?float
    {
        if (!$this->collection_date || !$this->reporting_date) {
            return null;
        }

        return round($this->collection_date->diffInHours($this->reporting_date), 1);
    }

    /**
     * Format reference range with proper line breaks.
     */
    protected function formatReferenceRange($param): string
    {
        if (!$param->reference_range) {
            return '';
        }

        // Replace stored '\n' with actual line breaks
        return str_replace('\n', "\n", $param->reference_range);
    }

    /**
     * Get the expected parameters for this order's test type.
     */
    public function getExpectedParametersAttribute()
    {
        if ($this->testType) {
            return $this->testType->parameters()
                ->orderBy('order')
                ->get()
                ->map(function ($param) {
                    $param->formatted_range = $this->formatReferenceRange($param);
                    return $param;
                });
        }

        // If it's a multi-test order (lab_test_type_id is null)
        if ($this->items->count() > 0) {
            $parameters = collect();
            foreach ($this->items as $item) {
                if ($item->labTestType) {
                    $itemParams = $item->labTestType->parameters()
                        ->orderBy('order')
                        ->get()
                        ->map(function ($param) {
                            $param->formatted_range = $this->formatReferenceRange($param);
                            return $param;
                        });
                    $parameters = $parameters->concat($itemParams);
                }
            }
            return $parameters;
        }

        return collect([]);
    }

    /**
     * Accessor for test_code (mapped to lab_number)
     */
    public function getTestCodeAttribute(): string
    {
        return $this->lab_number ?? '';
    }

    /**
     * Accessor for test_name (prioritizes stored test_name, falls back to testType name or parsed from comments)
     */
    public function getTestNameAttribute(): string
    {
        // 1. Explicitly set attribute (e.g. from DB)
        if (!empty($this->attributes['test_name'])) {
            return $this->attributes['test_name'];
        }

        // 2. Aggregate from multiple items if they exist
        if ($this->items->count() > 0) {
            $names = $this->items->map(function ($item) {
                return $item->labTestType ? $item->labTestType->name : null;
            })->filter()->unique();

            if ($names->count() > 0) {
                return $names->implode(', ');
            }
        }

        // 3. Fallback to testType relationship
        if ($this->testType) {
            return $this->testType->name;
        }

        // 4. Fallback: parse from comments
        if ($this->comments && preg_match('/Test:\s*([^|]+)/', $this->comments, $matches)) {
            return trim($matches[1]);
        }

        return 'Mixed Lab Report';
    }

    /**
     * Accessor for test_type (department)
     */
    public function getDepartmentAttribute()
    {
        if (!empty($this->attributes['test_type'])) {
            return $this->attributes['test_type'];
        }

        if ($this->labTestType && $this->labTestType->department) {
            return $this->labTestType->department;
        }

        return 'General';
    }

    /**
     * Accessor for sample_type
     */
    public function getSampleTypeAttribute(): string
    {
        if (!empty($this->attributes['sample_type'])) {
            return $this->attributes['sample_type'];
        }

        if ($this->testType && $this->testType->sample_type) {
            return $this->testType->sample_type;
        }

        return 'Blood';
    }

    /**
     * Accessor for interpretation - handles literal \n
     */
    public function getInterpretationAttribute($value): ?string
    {
        return preg_replace('/\\\\n/', "\n", $value ?? '');
    }

    /**
     * Accessor for recommendations - handles literal \n
     */
    public function getRecommendationsAttribute($value): ?string
    {
        return preg_replace('/\\\\n/', "\n", $value ?? '');
    }

    /**
     * Mutator for test_name to ensure it's always set
     */
    public function setTestNameAttribute($value)
    {
        $this->attributes['test_name'] = $value ?: ($this->testType ? $this->testType->name : null);
    }

    /**
     * Mutator for test_type to ensure it's always set
     */
    public function setTestTypeAttribute($value)
    {
        $this->attributes['test_type'] = $value ?: ($this->testType ? $this->testType->department : null);
    }

    /**
     * Mutator for sample_type to ensure it's always set
     */
    public function setSampleTypeAttribute($value)
    {
        $this->attributes['sample_type'] = $value ?: ($this->testType ? $this->testType->sample_type : 'Blood');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set test_name, test_type, sample_type when lab_test_type_id is set
        static::saving(function ($model) {
            if ($model->lab_test_type_id && !$model->test_name) {
                $testType = $model->testType;
                if ($testType) {
                    $model->test_name = $testType->name;
                    $model->test_type = $testType->department;
                    $model->sample_type = $testType->sample_type ?? 'Blood';
                }
            }
        });

        // Auto-generate lab number if not set
        static::creating(function ($model) {

            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->lab_number)) {
                $model->lab_number = static::generateLabNumber();
            }

            if (empty($model->priority)) {
                $model->priority = self::PRIORITY_NORMAL;
            }

            if (empty($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
        });
    }
}
