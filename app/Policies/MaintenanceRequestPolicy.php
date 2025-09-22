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
            // Use loose comparison "==" not "===" due to database driver differences
            // between local (SQLite) and production (MySQL)
            return $user->id == $maintenanceRequest->tenant_id;
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
        return $this->roleService->roleHasPermission($user, 'manage_properties');
    }

    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only managers/admins can delete, and only if status is 'submitted'
        return $user->organization_id === $maintenanceRequest->organization_id &&
               $this->roleService->roleHasPermission($user->role, 'manage_properties') &&
               $maintenanceRequest->status === 'submitted';
    }
}
