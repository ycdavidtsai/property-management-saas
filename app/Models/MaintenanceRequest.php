<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'organization_id',
        'property_id',
        'unit_id',
        'tenant_id',
        'assigned_vendor_id',
        'assigned_by',
        'assignment_notes',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'photos',
        'preferred_date',
        'assigned_at',
        'started_at',
        'completed_at',
        'closed_at',
        'estimated_cost',
        'actual_cost',
        'completion_notes',
        'tenant_rating',
        'tenant_feedback',
    ];

    protected $casts = [
        'photos' => 'array',
        'preferred_date' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'tenant_rating' => 'integer',
        'tenant_id' => 'integer', // Ensure tenant_id is cast to integer
        'assigned_vendor_id' => 'integer', // Ensure assigned_vendor_id is cast to integer
        'assigned_by' => 'integer', // Ensure assigned_by is cast to integer
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function assignedVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'assigned_vendor_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MaintenanceRequestUpdate::class)->orderBy('created_at');
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'emergency' => 'red',
            'high' => 'orange',
            'normal' => 'blue',
            'low' => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'yellow',
            'assigned' => 'blue',
            'in_progress' => 'indigo',
            'completed' => 'green',
            'closed' => 'gray',
        };
    }

    public function canBeAssigned(): bool
    {
        return in_array($this->status, ['submitted']);
    }

    public function canBeStarted(): bool
    {
        return in_array($this->status, ['assigned']);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['in_progress']);
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, ['completed']);
    }

    /**
     * Get the vendor assigned to this maintenance request
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'assigned_vendor_id');
    }

}
