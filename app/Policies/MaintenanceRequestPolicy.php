<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the maintenance request as a vendor.
     */
    public function viewAsVendor(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Vendor can view if they are assigned to this request
        // Check if user has a vendor record and that vendor is assigned
        if (!$user->vendor) {
            return false;
        }

        return $maintenanceRequest->assigned_vendor_id === $user->vendor->id;
    }

    /**
     * Determine if the vendor user can update the status of the maintenance request.
     * This is used for accept/reject actions and status transitions.
     */
    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Must be a vendor user
        if (!$user->vendor) {
            return false;
        }

        // Must be assigned to this request
        if ($maintenanceRequest->assigned_vendor_id !== $user->vendor->id) {
            return false;
        }

        // Vendor can update status in these cases:
        // 1. Accept/reject when status is pending_acceptance
        // 2. Change status when already accepted (assigned -> in_progress -> completed)

        $allowedStatuses = ['pending_acceptance', 'assigned', 'in_progress'];

        return in_array($maintenanceRequest->status, $allowedStatuses);
    }

    /**
     * Determine if the user can view the maintenance request.
     * Used for landlords/managers/tenants.
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin/Manager/Landlord can view any request in their organization
        if (in_array($user->role, ['admin', 'manager', 'landlord'])) {
            return $maintenanceRequest->organization_id === $user->organization_id;
        }

        // Tenant can view their own requests
        if ($user->role === 'tenant') {
            return $maintenanceRequest->tenant_id === $user->id;
        }

        // Vendor can view if assigned
        if ($user->role === 'vendor' && $user->vendor) {
            return $maintenanceRequest->assigned_vendor_id === $user->vendor->id;
        }

        return false;
    }

    /**
     * Determine if the user can create maintenance requests.
     */
    public function create(User $user): bool
    {
        // Tenants, landlords, managers, and admins can create requests
        return in_array($user->role, ['tenant', 'landlord', 'manager', 'admin']);
    }

    /**
     * Determine if the user can update the maintenance request.
     * Used for landlords/managers to edit details.
     */
    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin/Manager/Landlord can update requests in their organization
        if (in_array($user->role, ['admin', 'manager', 'landlord'])) {
            return $maintenanceRequest->organization_id === $user->organization_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the maintenance request.
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only admin/manager can delete requests in their organization
        if (in_array($user->role, ['admin', 'manager'])) {
            return $maintenanceRequest->organization_id === $user->organization_id;
        }

        return false;
    }

    /**
     * Determine if the user can assign vendors to the maintenance request.
     */
    public function assignVendor(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin/Manager/Landlord can assign vendors in their organization
        if (in_array($user->role, ['admin', 'manager', 'landlord'])) {
            return $maintenanceRequest->organization_id === $user->organization_id;
        }

        return false;
    }
}
