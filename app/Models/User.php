<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // Update fillable array to include new fields
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'role',
        'permissions',
        'phone',
        'profile_photo_path',
        'emergency_contact',
        'notes',
        'is_active',
        'notification_preferences', // ← MUST BE HERE!
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * ✅ FIXED: Consolidated all casts into one method
     * ✅ REMOVED: Duplicate $casts property that was causing conflicts
     *
     * @return array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'emergency_contact' => 'array',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'notification_preferences' => 'array', // ✅ Now this will work!
        ];
    }

    // ===== RELATIONSHIPS =====

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function belongsToOrganization($organizationId): bool
    {
        return $this->organization_id == $organizationId;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isTenant(): bool
    {
        // Delegate to RoleService instance
        $roleService = new \App\Services\RoleService();
        return $roleService->isTenant($this);
    }
    public function isLandlord(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'landlord']);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }
    /**
     * Get the vendor profile associated with this user (if role is vendor)
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    // Relationships
    public function tenantProfile()
    {
        return $this->hasOne(TenantProfile::class);
    }

    // Add these methods to your existing User model relationships section



    /**
     * Lease relationships for tenant users
     */
    public function leases(): BelongsToMany
    {
        return $this->belongsToMany(Lease::class, 'lease_tenant', 'tenant_id', 'lease_id');
    }

    /**
     * Get the active lease for this tenant
     *
     * @return Lease|null
     */
    public function activeLease()
    {
        //return $this->leases()->where('status', 'active')->first();
        return $this->leases()
        ->whereIn('status', ['active', 'expiring_soon'])
        ->first();
    }

    /**
     * Check if this tenant has an active lease
     */
    public function hasActiveLease(): bool
    {
        return $this->activeLease() !== null;
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'tenant_id');
    }

    public function assignedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_vendor_id');
    }

    // public function payments()
    // {
    //     return $this->hasMany(Payment::class, 'tenant_id');
    // }

    // Scopes for filtering by role
    public function scopeOfRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeTenants($query)
    {
        return $query->where('role', 'tenant');
    }

    public function scopeLandlords($query)
    {
        return $query->whereIn('role', ['admin', 'manager', 'landlord']);
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    // Permission checking
    public function hasPermission(string $permission): bool
    {
        // Admin and manager have all permissions
        // if (in_array($this->role, ['admin', 'manager'])) {
        //     return true;
        // }

        // $userPermissions = $this->permissions ?? [];
        // return in_array($permission, $userPermissions);

        // Use RoleService to check permission, suggested by co-poilot
        return \App\Services\RoleService::roleHasPermission($this->role, $permission);
    }

    // Add these methods to your existing app/Models/User.php file
    // Place in the relationships/tenant section

    /**
     * Get current property through active lease
     */
    public function currentProperty()
    {
        $activeLease = $this->activeLease();
        return $activeLease ? $activeLease->unit->property : null;
    }

    /**
     * Get current unit through active lease
     */
    public function currentUnit()
    {
        $activeLease = $this->activeLease();
        return $activeLease ? $activeLease->unit : null;
    }

    /**
     * Get lease history for this tenant
     */
    public function leaseHistory()
    {
        return $this->leases()->with(['unit.property'])->orderBy('start_date', 'desc');
    }

    /**
     * Check if tenant is currently housed
     */
    public function isCurrentlyHoused(): bool
    {
        return $this->currentProperty() !== null;
    }

    /**
     * Get formatted address for current residence
     */
    public function getCurrentAddressAttribute(): string
    {
        $property = $this->currentProperty();
        $unit = $this->currentUnit();

        if (!$property || !$unit) {
            return 'No current residence';
        }

        return "{$property->address}, Unit {$unit->unit_number}";
    }

    // Add to your existing User model

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'to_user_id');
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'from_user_id');
    }

    public function broadcastMessages()
    {
        return $this->hasMany(BroadcastMessage::class, 'sender_id');
    }

    // Helper to get unread notification count
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Broadcast messages sent by this user
     */
    public function sentBroadcasts(): HasMany
    {
        return $this->hasMany(BroadcastMessage::class, 'sender_id');
    }

    /**
     * ✨ NEW: Sync user changes to vendor record if user is a vendor
     */
    public function syncToVendor(): void
    {
        // Only sync if user is a vendor
        if ($this->role !== 'vendor') {
            return;
        }

        // Find vendor record linked to this user
        $vendor = Vendor::where('user_id', $this->id)->first();

        if ($vendor) {
            $vendor->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            \Log::info('User profile synced to vendor', [
                'user_id' => $this->id,
                'vendor_id' => $vendor->id,
            ]);
        }
    }

}
