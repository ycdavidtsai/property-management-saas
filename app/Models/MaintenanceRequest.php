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
        'accepted_at',           // ← NEW: When vendor accepted
        'started_at',
        'completed_at',
        'closed_at',
        'estimated_cost',
        'actual_cost',
        'completion_notes',
        'tenant_rating',
        'tenant_feedback',
        'rejection_reason',      // ← NEW: Why vendor rejected
        'rejection_notes',       // ← NEW: Additional rejection details
        'rejected_at',           // ← NEW: When rejected
        'rejected_by',           // ← NEW: Which vendor rejected
    ];

    protected $casts = [
        'photos' => 'array',
        'preferred_date' => 'datetime',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',      // ← NEW
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'rejected_at' => 'datetime',      // ← NEW
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'tenant_rating' => 'integer',
        'tenant_id' => 'integer',
        'assigned_vendor_id' => 'integer',
        'assigned_by' => 'integer',
        'rejected_by' => 'integer',       // ← NEW
    ];

    // Relationships
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

    public function rejectedBy(): BelongsTo  // ← NEW: Vendor who rejected
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MaintenanceRequestUpdate::class);
    }

    /**
     * Get the vendor assigned to this maintenance request
     * (Alias for assignedVendor)
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'assigned_vendor_id');
    }

    // Accessors
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'emergency' => 'red',
            'high' => 'orange',
            'normal' => 'blue',
            'low' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'yellow',
            'pending_acceptance' => 'yellow',  // ← NEW: Same color as submitted
            'assigned' => 'blue',
            'in_progress' => 'indigo',
            'completed' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string  // ← NEW: User-friendly labels
    {
        return match($this->status) {
            'submitted' => 'Submitted',
            'pending_acceptance' => 'Pending Acceptance',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'closed' => 'Closed',
            default => ucfirst($this->status),
        };
    }

    public function getRejectionReasonTextAttribute(): ?string  // ← NEW: Human-readable rejection reason
    {
        if (!$this->rejection_reason) {
            return null;
        }

        $reasonMap = [
            'too_busy' => 'Currently too busy / Fully booked',
            'out_of_area' => 'Property location outside service area',
            'lacks_expertise' => 'Requires specialized expertise',
            'emergency_unavailable' => 'Cannot handle emergency priority at this time',
            'insufficient_info' => 'Insufficient information to assess job',
            'other' => 'Other reason',
        ];

        return $reasonMap[$this->rejection_reason] ?? $this->rejection_reason;
    }

    // Status check methods (updated for new flow)
    public function canBeAssigned(): bool
    {
        return in_array($this->status, ['submitted']);
    }

    public function canBeAccepted(): bool  // ← NEW: Vendor can accept
    {
        return $this->status === 'pending_acceptance';
    }

    public function canBeRejected(): bool  // ← NEW: Vendor can reject
    {
        return $this->status === 'pending_acceptance';
    }

    public function canBeStarted(): bool
    {
        // Can only start work after acceptance
        return $this->status === 'assigned' && $this->accepted_at !== null;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['in_progress']);
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, ['completed']);
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopePendingAcceptance($query)  // ← NEW
    {
        return $query->where('status', 'pending_acceptance');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('assigned_vendor_id', $vendorId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
