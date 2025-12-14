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
}
