<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Services\RoleService;

class MaintenanceRequestPolicy
{
    public function __construct(private RoleService $roleService)
    {
    }

    public function viewAny(User $user): bool
    {
        return true; // Organization middleware handles access
    }

    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Check organization access
        if ($user->organization_id !== $maintenanceRequest->organization_id) {
            return false;
        }

        // Tenants can only see their own requests
        if ($this->roleService->isTenant($user)) {
            // Debug logging for live server
            \Log::info('Tenant Authorization Check:', [
                'user_id' => $user->id,
                'user_id_type' => gettype($user->id),
                'request_tenant_id' => $maintenanceRequest->tenant_id,
                'request_tenant_id_type' => gettype($maintenanceRequest->tenant_id),
                'ids_match' => $user->id === $maintenanceRequest->tenant_id,
                'ids_equal_loose' => $user->id == $maintenanceRequest->tenant_id,
            ]);

            return $user->id == $maintenanceRequest->tenant_id; // Use == instead of ===
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create requests
    }

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

        // Management users can always edit
        return $this->roleService->hasPermission($user, 'manage_properties');
    }

    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only managers/admins can delete, and only if status is 'submitted'
        return $user->organization_id === $maintenanceRequest->organization_id &&
               $this->roleService->hasPermission($user, 'manage_properties') &&
               $maintenanceRequest->status === 'submitted';
    }
}
