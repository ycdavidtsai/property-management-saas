<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\VendorInvoice;

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
        'scheduled_date',
        'scheduled_start_time',
        'scheduled_end_time',
        'scheduling_status',
        'proposed_times',
        'appointment_confirmed_at',
        'tenant_notified_on_way',
        'on_way_notified_at',
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
        'scheduled_date' => 'date',
        'proposed_times' => 'array',
        'appointment_confirmed_at' => 'datetime',
        'tenant_notified_on_way' => 'boolean',
        'on_way_notified_at' => 'datetime',
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

    // =============================================
    // 3. ADD THIS RELATIONSHIP
    // =============================================

    /**
     * Invoice for this maintenance request (if completed)
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(VendorInvoice::class);
    }


    // =============================================
    // 4. ADD THESE NEW ACCESSORS
    // =============================================

    /**
     * Get formatted scheduled date and time for display
     */
    public function getFormattedScheduleAttribute(): ?string
    {
        if (!$this->scheduled_date) {
            return null;
        }

        $date = $this->scheduled_date->format('D, M j, Y');

        if ($this->scheduled_start_time && $this->scheduled_end_time) {
            $start = date('g:i A', strtotime($this->scheduled_start_time));
            $end = date('g:i A', strtotime($this->scheduled_end_time));
            return "{$date} at {$start} - {$end}";
        }

        return $date;
    }

    /**
     * Get scheduling status label for display
     */
    public function getSchedulingStatusLabelAttribute(): ?string
    {
        if (!$this->scheduling_status) {
            return null;
        }

        return match($this->scheduling_status) {
            'pending_tenant_proposal' => 'Awaiting Tenant',
            'pending_vendor_confirmation' => 'Awaiting Vendor',
            'confirmed' => 'Confirmed',
            'rescheduled' => 'Rescheduling',
            default => ucfirst(str_replace('_', ' ', $this->scheduling_status)),
        };
    }

    /**
     * Get scheduling status color for badges
     */
    public function getSchedulingStatusColorAttribute(): string
    {
        return match($this->scheduling_status) {
            'pending_tenant_proposal' => 'yellow',
            'pending_vendor_confirmation' => 'blue',
            'confirmed' => 'green',
            'rescheduled' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Check if appointment is today
     */
    public function getIsScheduledTodayAttribute(): bool
    {
        return $this->scheduled_date && $this->scheduled_date->isToday();
    }

    /**
     * Check if appointment is in the past
     */
    public function getIsAppointmentPastAttribute(): bool
    {
        if (!$this->scheduled_date) {
            return false;
        }

        if ($this->scheduled_date->isFuture()) {
            return false;
        }

        if ($this->scheduled_date->isToday() && $this->scheduled_end_time) {
            return now()->format('H:i') > $this->scheduled_end_time;
        }

        return $this->scheduled_date->isPast();
    }

    /**
     * Get proposed times in a readable format
     */
    public function getProposedTimesSummaryAttribute(): ?string
    {
        if (!$this->proposed_times || empty($this->proposed_times['slots'])) {
            return null;
        }

        $slots = $this->proposed_times['slots'];
        $summaries = [];

        foreach (array_slice($slots, 0, 3) as $slot) {
            $date = \Carbon\Carbon::parse($slot['date'])->format('M j');
            $time = date('g:i A', strtotime($slot['start']));
            $summaries[] = "{$date} at {$time}";
        }

        return implode(' | ', $summaries);
    }


    // =============================================
    // 5. ADD THESE STATUS CHECK METHODS
    // =============================================

    /**
     * Check if scheduling is needed (vendor accepted, no appointment yet)
     */
    public function needsScheduling(): bool
    {
        return $this->status === 'assigned'
            && $this->accepted_at
            && $this->scheduling_status !== 'confirmed';
    }

    /**
     * Check if tenant needs to propose times
     */
    public function awaitingTenantProposal(): bool
    {
        return $this->scheduling_status === 'pending_tenant_proposal';
    }

    /**
     * Check if vendor needs to confirm appointment
     */
    public function awaitingVendorConfirmation(): bool
    {
        return $this->scheduling_status === 'pending_vendor_confirmation';
    }

    /**
     * Check if appointment is confirmed
     */
    public function hasConfirmedAppointment(): bool
    {
        return $this->scheduling_status === 'confirmed'
            && $this->scheduled_date
            && $this->appointment_confirmed_at;
    }

    /**
     * Check if "On My Way" notification can be sent
     */
    public function canSendOnWayNotification(): bool
    {
        return $this->hasConfirmedAppointment()
            && !$this->tenant_notified_on_way
            && in_array($this->status, ['assigned', 'in_progress'])
            && $this->scheduled_date->isToday();
    }


    // =============================================
    // 6. ADD THESE ACTION METHODS
    // =============================================

    /**
     * Set proposed times from tenant
     */
    public function setProposedTimes(array $slots, ?string $notes = null): void
    {
        $this->update([
            'proposed_times' => [
                'proposed_by' => 'tenant',
                'proposed_at' => now()->toIso8601String(),
                'slots' => $slots,
                'tenant_notes' => $notes,
            ],
            'scheduling_status' => 'pending_vendor_confirmation',
        ]);
    }

    /**
     * Confirm appointment with selected slot
     */
    public function confirmAppointment(string $date, string $startTime, string $endTime): void
    {
        $this->update([
            'scheduled_date' => $date,
            'scheduled_start_time' => $startTime,
            'scheduled_end_time' => $endTime,
            'scheduling_status' => 'confirmed',
            'appointment_confirmed_at' => now(),
        ]);
    }

    /**
     * Request tenant to propose new times (vendor couldn't make any proposed times)
     */
    public function requestNewProposal(): void
    {
        $this->update([
            'scheduling_status' => 'pending_tenant_proposal',
            'proposed_times' => null,
        ]);
    }

    /**
     * Mark that "On My Way" notification was sent
     */
    public function markOnWayNotified(): void
    {
        $this->update([
            'tenant_notified_on_way' => true,
            'on_way_notified_at' => now(),
        ]);
    }


    // =============================================
    // 7. ADD THESE SCOPES
    // =============================================

    /**
     * Scope: Requests needing scheduling
     */
    public function scopeNeedsScheduling($query)
    {
        return $query->where('status', 'assigned')
                    ->whereNotNull('accepted_at')
                    ->where(function($q) {
                        $q->whereNull('scheduling_status')
                        ->orWhere('scheduling_status', '!=', 'confirmed');
                    });
    }

    /**
     * Scope: Requests with confirmed appointments
     */
    public function scopeWithConfirmedAppointment($query)
    {
        return $query->where('scheduling_status', 'confirmed')
                    ->whereNotNull('scheduled_date');
    }

    /**
     * Scope: Requests scheduled for today
     */
    public function scopeScheduledToday($query)
    {
        return $query->whereDate('scheduled_date', today())
                    ->where('scheduling_status', 'confirmed');
    }

    /**
     * Scope: Requests scheduled for a specific date
     */
    public function scopeScheduledOn($query, $date)
    {
        return $query->whereDate('scheduled_date', $date)
                    ->where('scheduling_status', 'confirmed');
    }

    /**
     * Scope: Requests awaiting tenant to propose times
     */
    public function scopeAwaitingTenantProposal($query)
    {
        return $query->where('scheduling_status', 'pending_tenant_proposal');
    }

    /**
     * Scope: Requests awaiting vendor to confirm times
     */
    public function scopeAwaitingVendorConfirmation($query)
    {
        return $query->where('scheduling_status', 'pending_vendor_confirmation');
    }

}
