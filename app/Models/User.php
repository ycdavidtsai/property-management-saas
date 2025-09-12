<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    // Update casts array
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'emergency_contact' => 'array',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role checking methods
    public function isTenant(): bool
    {
        return $this->role === 'tenant';
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

    // Relationships
    public function tenantProfile()
    {
        return $this->hasOne(TenantProfile::class);
    }

    // public function leases()
    // {
    //     return $this->hasMany(Lease::class, 'tenant_id');
    // }

    // public function activeLeases()
    // {
    //     return $this->leases()->where('status', 'active');
    // }

    // public function maintenanceRequests()
    // {
    //     return $this->hasMany(MaintenanceRequest::class, 'tenant_id');
    // }

    // public function assignedMaintenanceRequests()
    // {
    //     return $this->hasMany(MaintenanceRequest::class, 'assigned_vendor_id');
    // }

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
        if (in_array($this->role, ['admin', 'manager'])) {
            return true;
        }

        $userPermissions = $this->permissions ?? [];
        return in_array($permission, $userPermissions);
    }

}
