<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use App\Services\RoleService;

class VendorPolicy
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Determine if the user can view any vendors
     */
    public function viewAny(User $user): bool
    {
        // Only admin, manager, and landlord can view vendors
        return $this->roleService->hasPermission($user, 'vendors.view')
            || in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if the user can view the vendor
     */
    public function view(User $user, Vendor $vendor): bool
    {
        // Must be in same organization and have proper role
        if ($user->organization_id !== $vendor->organization_id) {
            return false;
        }

        return $this->roleService->hasPermission($user, 'vendors.view')
            || in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if the user can create vendors
     */
    public function create(User $user): bool
    {
        // Only admin, manager, and landlord can create vendors
        return $this->roleService->hasPermission($user, 'vendors.create')
            || in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if the user can update the vendor
     */
    public function update(User $user, Vendor $vendor): bool
    {
        // Must be in same organization and have proper role
        if ($user->organization_id !== $vendor->organization_id) {
            return false;
        }

        return $this->roleService->hasPermission($user, 'vendors.update')
            || in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if the user can delete the vendor
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        // Must be in same organization and have proper role
        if ($user->organization_id !== $vendor->organization_id) {
            return false;
        }

        return $this->roleService->hasPermission($user, 'vendors.delete')
            || in_array($user->role, ['admin', 'manager']);
    }
}
