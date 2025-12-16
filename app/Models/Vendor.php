<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\VendorPromotionRequest;


class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'business_type',
        'description',
        'specialties',
        'is_active',
        'hourly_rate',
        'notes',
        'user_id',
        'vendor_type',
        'created_by_organization_id',
        'promoted_at',
        'promotion_fee_paid',
        // NEW: Invitation/Setup fields
        'setup_status',
        'invitation_token',
        'invitation_sent_at',
        'invitation_expires_at',
        'invitation_resend_count',
        'last_invitation_sent_at',
        'phone_verification_code',
        'phone_verification_expires_at',
        'phone_verified_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        // New: Self-registration Approval fields
        'contact_name',
        'approved_by',
        'approved_at',
        'registration_source',  // ADD THIS - it's missing!
        'availability_schedule',
        'service_areas',
        'portfolio_photos',
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'organization_id' => 'string',
        'promoted_at' => 'datetime',
        'promotion_fee_paid' => 'decimal:2',
        // NEW: Date casts for invitation fields
        'invitation_sent_at' => 'datetime',
        'invitation_expires_at' => 'datetime',
        'last_invitation_sent_at' => 'datetime',
        'phone_verification_expires_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'approved_at' => 'datetime',
        'availability_schedule' => 'array',
        'service_areas' => 'array',
        'portfolio_photos' => 'array',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Many-to-many: Vendor can work for multiple organizations
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_vendor')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_vendor_id');
    }

    /**
     * Organization that created this vendor (for private vendors)
     */
    public function creator()
    {
        return $this->belongsTo(Organization::class, 'created_by_organization_id');
    }

    /**
     * Promotion requests for this vendor
     */
    public function promotionRequests()
    {
        return $this->hasMany(VendorPromotionRequest::class);
    }

    /**
     * User account linked to this vendor (for portal access)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * User who rejected this vendor (for self-registration rejections)
     */
    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getFormattedSpecialtiesAttribute(): string
    {
        return is_array($this->specialties) ? implode(', ', $this->specialties) : '';
    }

    /**
     * Get display-friendly setup status
     */
    public function getSetupStatusLabelAttribute(): string
    {
        return match($this->setup_status) {
            'pending_setup' => 'Invitation Pending',
            'pending_approval' => 'Awaiting Approval',
            'active' => 'Active',
            'rejected' => 'Rejected',
            default => ucfirst($this->setup_status ?? 'Unknown'),
        };
    }

    /**
     * Get setup status color for badges
     */
    public function getSetupStatusColorAttribute(): string
    {
        return match($this->setup_status) {
            'pending_setup' => 'yellow',
            'pending_approval' => 'blue',
            'active' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    // ============================================
    // STATUS CHECKS
    // ============================================

    /**
     * Check if vendor is global
     */
    public function isGlobal(): bool
    {
        return $this->vendor_type === 'global';
    }

    /**
     * Check if vendor is private
     */
    public function isPrivate(): bool
    {
        return $this->vendor_type === 'private';
    }

    /**
     * Check if vendor is owned by an organization
     */
    public function isOwnedBy($organizationId): bool
    {
        return $this->created_by_organization_id == $organizationId;
    }

    /**
     * Check if vendor is managed by user account
     */
    public function isManagedByUser(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Check if vendor can be edited by landlord
     */
    public function canBeEditedByLandlord(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Check if vendor is pending setup (invitation sent but not completed)
     */
    public function isPendingSetup(): bool
    {
        return $this->setup_status === 'pending_setup';
    }

    /**
     * Check if vendor is pending approval (self-registered)
     */
    public function isPendingApproval(): bool
    {
        return $this->setup_status === 'pending_approval';
    }

    /**
     * Check if vendor is fully active
     */
    public function isFullyActive(): bool
    {
        return $this->is_active && $this->setup_status === 'active';
    }

    /**
     * Check if invitation is expired
     */
    public function isInvitationExpired(): bool
    {
        return $this->invitation_expires_at && $this->invitation_expires_at->isPast();
    }

    /**
     * Check if phone is verified
     */
    public function isPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    // ============================================
    // PERMISSIONS
    // ============================================

    /**
     * Check if vendor can be edited by user
     */
    public function canBeEditedBy(User $user): bool
    {
        // Admins can edit all vendors
        if ($user->role === 'admin') {
            return true;
        }

        // For private vendors, only the creating organization can edit
        if ($this->isPrivate()) {
            return $this->created_by_organization_id === $user->organization_id;
        }

        // Global vendors can only be edited by admin
        return false;
    }

    /**
     * Check if vendor can receive job assignments
     */
    public function canReceiveAssignments(): bool
    {
        return $this->is_active &&
               $this->setup_status === 'active' &&
               !is_null($this->user_id);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Global vendors only
     */
    public function scopeGlobal($query)
    {
        return $query->where('vendor_type', 'global');
    }

    /**
     * Scope: Private vendors only
     */
    public function scopePrivate($query)
    {
        return $query->where('vendor_type', 'private');
    }

    /**
     * Scope: Vendors visible to an organization
     */
    public function scopeVisibleToOrganization($query, $organizationId)
    {
        return $query->where(function($q) use ($organizationId) {
            $q->where('vendor_type', 'global')
              ->orWhere('created_by_organization_id', $organizationId);
        });
    }

    /**
     * Scope: Fully active vendors only
     */
    public function scopeFullyActive($query)
    {
        return $query->where('is_active', true)
                     ->where('setup_status', 'active');
    }

    /**
     * Scope: Vendors pending setup
     */
    public function scopePendingSetup($query)
    {
        return $query->where('setup_status', 'pending_setup');
    }

    /**
     * Scope: Vendors pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('setup_status', 'pending_approval');
    }

    /**
     * Scope: Vendors that can receive assignments
     */
    public function scopeAssignable($query)
    {
        return $query->where('is_active', true)
                     ->where('setup_status', 'active')
                     ->whereNotNull('user_id');
    }

    // ============================================
    // ACTIONS
    // ============================================

    /**
     * Sync vendor data from linked user account
     */
    public function syncFromUser(User $user): void
    {
        $this->update([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    // =============================================
    // 3. ADD THESE NEW RELATIONSHIPS
    // =============================================

    /**
     * Invoices issued by this vendor
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(VendorInvoice::class);
    }

    /**
     * Pending (unpaid) invoices
     */
    public function pendingInvoices(): HasMany
    {
        return $this->hasMany(VendorInvoice::class)->where('status', 'pending');
    }


    // =============================================
    // 4. ADD THESE NEW ACCESSORS
    // =============================================

    /**
     * Get formatted availability for display
     * Returns human-readable schedule like "Mon-Fri 8am-5pm"
     */
    public function getAvailabilitySummaryAttribute(): string
    {
        if (!$this->availability_schedule || !isset($this->availability_schedule['weekly'])) {
            return 'Not set';
        }

        $weekly = $this->availability_schedule['weekly'];
        $availableDays = [];

        foreach ($weekly as $day => $schedule) {
            if ($schedule['available'] ?? false) {
                $availableDays[$day] = $schedule;
            }
        }

        if (empty($availableDays)) {
            return 'Not available';
        }

        // Check if all weekdays have same hours
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $weekend = ['saturday', 'sunday'];

        $weekdaySchedules = array_intersect_key($availableDays, array_flip($weekdays));
        $weekendSchedules = array_intersect_key($availableDays, array_flip($weekend));

        $summary = [];

        // Check if weekdays are consistent
        if (count($weekdaySchedules) === 5) {
            $times = array_unique(array_map(fn($s) => $s['start'] . '-' . $s['end'], $weekdaySchedules));
            if (count($times) === 1) {
                $first = reset($weekdaySchedules);
                $summary[] = 'Mon-Fri ' . $this->formatTimeRange($first['start'], $first['end']);
            }
        } elseif (count($weekdaySchedules) > 0) {
            // List individual days
            foreach ($weekdaySchedules as $day => $schedule) {
                $summary[] = ucfirst(substr($day, 0, 3)) . ' ' . $this->formatTimeRange($schedule['start'], $schedule['end']);
            }
        }

        // Add weekend if available
        foreach ($weekendSchedules as $day => $schedule) {
            $summary[] = ucfirst(substr($day, 0, 3)) . ' ' . $this->formatTimeRange($schedule['start'], $schedule['end']);
        }

        return implode(', ', $summary) ?: 'Not available';
    }

    /**
     * Format time range for display
     */
    protected function formatTimeRange(string $start, string $end): string
    {
        $startFormatted = date('ga', strtotime($start));
        $endFormatted = date('ga', strtotime($end));
        return $startFormatted . '-' . $endFormatted;
    }

    /**
     * Get service areas as comma-separated string
     */
    public function getServiceAreasSummaryAttribute(): string
    {
        if (!$this->service_areas || empty($this->service_areas['areas'])) {
            return 'Not set';
        }

        $areas = $this->service_areas['areas'];

        if (count($areas) <= 3) {
            return implode(', ', $areas);
        }

        return implode(', ', array_slice($areas, 0, 3)) . ' +' . (count($areas) - 3) . ' more';
    }

    /**
     * Get count of portfolio photos
     */
    public function getPortfolioCountAttribute(): int
    {
        return is_array($this->portfolio_photos) ? count($this->portfolio_photos) : 0;
    }

    /**
     * Get total earnings for current month
     */
    public function getCurrentMonthEarningsAttribute(): float
    {
        return $this->invoices()
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
    }

    /**
     * Get total pending invoice amount
     */
    public function getPendingInvoiceAmountAttribute(): float
    {
        return $this->invoices()
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');
    }


    // =============================================
    // 5. ADD THESE NEW HELPER METHODS
    // =============================================

    /**
     * Check if vendor is available on a specific date and time
     */
    public function isAvailableAt(\DateTime $dateTime): bool
    {
        if (!$this->availability_schedule) {
            return true; // No schedule set = assume available
        }

        $dayOfWeek = strtolower($dateTime->format('l')); // monday, tuesday, etc.
        $time = $dateTime->format('H:i');
        $dateString = $dateTime->format('Y-m-d');

        // Check exceptions first
        $exceptions = $this->availability_schedule['exceptions'] ?? [];
        foreach ($exceptions as $exception) {
            if ($exception['date'] === $dateString && !($exception['available'] ?? false)) {
                return false;
            }
        }

        // Check weekly schedule
        $weekly = $this->availability_schedule['weekly'] ?? [];
        if (!isset($weekly[$dayOfWeek]) || !($weekly[$dayOfWeek]['available'] ?? false)) {
            return false;
        }

        $daySchedule = $weekly[$dayOfWeek];
        $start = $daySchedule['start'] ?? '00:00';
        $end = $daySchedule['end'] ?? '23:59';

        return $time >= $start && $time <= $end;
    }

    /**
     * Check if vendor serves a specific zip code or city
     */
    public function servesArea(string $area): bool
    {
        if (!$this->service_areas || empty($this->service_areas['areas'])) {
            return true; // No areas set = serves everywhere
        }

        $areas = array_map('strtolower', $this->service_areas['areas']);
        $areaLower = strtolower(trim($area));

        // For zip codes, also check if first 5 digits match
        if ($this->service_areas['type'] === 'zip_codes') {
            $areaZip = substr(preg_replace('/[^0-9]/', '', $area), 0, 5);
            foreach ($areas as $serviceArea) {
                if (substr($serviceArea, 0, 5) === $areaZip) {
                    return true;
                }
            }
        }

        return in_array($areaLower, $areas);
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlotsForDate(\DateTime $date, int $slotDurationMinutes = 60): array
    {
        $dayOfWeek = strtolower($date->format('l'));
        $dateString = $date->format('Y-m-d');

        // Check if this is an exception day
        $exceptions = $this->availability_schedule['exceptions'] ?? [];
        foreach ($exceptions as $exception) {
            if ($exception['date'] === $dateString && !($exception['available'] ?? false)) {
                return []; // Day off
            }
        }

        // Get weekly schedule for this day
        $weekly = $this->availability_schedule['weekly'] ?? [];
        if (!isset($weekly[$dayOfWeek]) || !($weekly[$dayOfWeek]['available'] ?? false)) {
            return [];
        }

        $daySchedule = $weekly[$dayOfWeek];
        $start = $daySchedule['start'] ?? '08:00';
        $end = $daySchedule['end'] ?? '17:00';

        // Generate slots
        $slots = [];
        $current = strtotime($dateString . ' ' . $start);
        $endTime = strtotime($dateString . ' ' . $end);

        while ($current + ($slotDurationMinutes * 60) <= $endTime) {
            $slotStart = date('H:i', $current);
            $slotEnd = date('H:i', $current + ($slotDurationMinutes * 60));

            $slots[] = [
                'start' => $slotStart,
                'end' => $slotEnd,
                'display' => date('g:i A', $current) . ' - ' . date('g:i A', $current + ($slotDurationMinutes * 60)),
            ];

            $current += $slotDurationMinutes * 60;
        }

        return $slots;
    }


    // =============================================
    // 6. ADD THIS SCOPE
    // =============================================

    /**
     * Scope: Vendors that serve a specific area
     */
    public function scopeServingArea($query, string $area)
    {
        return $query->where(function($q) use ($area) {
            // Include vendors with no service areas set (they serve everywhere)
            $q->whereNull('service_areas')
            ->orWhereJsonLength('service_areas->areas', 0);

            // Or vendors whose service areas include this area
            // Note: This is a simplified check - for production, consider
            // a more robust JSON search based on your database
            $q->orWhereJsonContains('service_areas->areas', $area);
        });
    }
}
