<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MaintenanceRequest;
use App\Services\RoleService;

class MaintenanceRequestPolicy
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Determine if the user can view any maintenance requests
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view the index
    }

    /**
     * Determine if the user can view the maintenance request
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Check organization access
        if ($user->organization_id !== $maintenanceRequest->organization_id) {
            return false;
        }

        // Tenants can only view their own requests
        if ($this->roleService->isTenant($user)) {
            return $user->id === $maintenanceRequest->tenant_id;
        }

        // Management users can view all requests
        return true;
    }

    /**
     * Determine if the user can create maintenance requests
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create requests
    }

    /**
     * Determine if the user can update the maintenance request
     */
    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Check organization access
        if ($user->organization_id !== $maintenanceRequest->organization_id) {
            return false;
        }

        // Tenants can only edit their own requests and only if status is 'submitted'
        if ($this->roleService->isTenant($user)) {
            return $user->id === $maintenanceRequest->tenant_id &&
                   $maintenanceRequest->status === 'submitted';
        }

        // Admin, Manager, and Landlord can update ANY request
        // This allows them to assign vendors, change status, etc.
        return in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if the user can delete the maintenance request
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Check organization access
        if ($user->organization_id !== $maintenanceRequest->organization_id) {
            return false;
        }

        // Only admin and manager, landlord can delete requests
        return in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if vendor can view this maintenance request
     * Vendors can only see requests assigned to them
     */
    public function viewAsVendor(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Must be a vendor
        if ($user->role !== 'vendor') {
            return false;
        }

        // Must have a vendor profile
        if (!$user->vendor) {
            return false;
        }

        // Must be in same organization
        if ($user->organization_id !== $maintenanceRequest->organization_id) {
            return false;
        }

        // Request must be assigned to this vendor
        return $maintenanceRequest->assigned_vendor_id === $user->vendor->id;
    }

    /**
     * Determine if vendor can update the status of this maintenance request
     */
    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Use same logic as viewAsVendor
        return $this->viewAsVendor($user, $maintenanceRequest);
    }

    /**
     * Determine if vendor can add updates to this maintenance request
     */
    public function addUpdate(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Use same logic as viewAsVendor
        return $this->viewAsVendor($user, $maintenanceRequest);
    }
}
