<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\VendorPromotionRequest;


class Vendor extends Model
{
    use HasFactory; // Removed HasUuids since we're using auto-incrementing IDs

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
        'user_id', // Link to users table
        'vendor_type',
        'created_by_organization_id',
        'promoted_at',
        'promotion_fee_paid',
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'organization_id' => 'string', // UUIDs stay strings
        'promoted_at' => 'datetime',
        'promotion_fee_paid' => 'decimal:2',
    ];

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

    public function getFormattedSpecialtiesAttribute(): string
    {
        return is_array($this->specialties) ? implode(', ', $this->specialties) : '';
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
     * Includes: Global vendors + Private vendors they created
     */
    public function scopeVisibleToOrganization($query, $organizationId)
    {
        return $query->where(function($q) use ($organizationId) {
            $q->where('vendor_type', 'global')
            ->orWhere('created_by_organization_id', $organizationId);
        });
    }

    /**
     * User account linked to this vendor (for portal access)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ✨ NEW: Sync vendor data from linked user account
     */
    public function syncFromUser(User $user): void
    {
        $this->update([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    /**
     * ✨ NEW: Check if vendor is managed by user account
     */
    public function isManagedByUser(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * ✨ NEW: Check if vendor can be edited by landlord
     */
    public function canBeEditedByLandlord(): bool
    {
        // Can only edit if no user account is linked
        return is_null($this->user_id);
    }
}
