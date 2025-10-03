<?php

namespace App\Services;

class RoleService
{
    /**
     * Define permissions for each role
     */
    public static function getPermissions(): array
    {
        return [
            'admin' => [
                // Properties
                'properties.view',
                'properties.create',
                'properties.edit',
                'properties.delete',

                // Units
                'units.view',
                'units.create',
                'units.edit',
                'units.delete',

                // Tenants
                'tenants.view',
                'tenants.create',
                'tenants.edit',
                'tenants.delete',

                // Leases - FULL CONTROL
                'leases.view',
                'leases.create',
                'leases.edit',
                'leases.delete',

                // Users
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',

                // Maintenance
                'maintenance.view',
                'maintenance.create',
                'maintenance.edit',
                'maintenance.delete',

                // Payments
                'payments.view',
                'payments.create',
                'payments.edit',
                'payments.delete',

                // Reports
                'reports.view',
                'reports.create',

                // Dashboard
                'dashboard.view',
            ],

            'manager' => [
                // Properties
                'properties.view',
                'properties.create',
                'properties.edit',

                // Units
                'units.view',
                'units.create',
                'units.edit',

                // Tenants
                'tenants.view',
                'tenants.create',
                'tenants.edit',
                'tenants.delete',

                // Leases - FULL CONTROL
                'leases.view',
                'leases.create',
                'leases.edit',
                'leases.delete',

                // Maintenance
                'maintenance.view',
                'maintenance.create',
                'maintenance.edit',
                'maintenance.delete',

                // Payments
                'payments.view',
                'payments.create',
                'payments.edit',

                // Reports
                'reports.view',

                // Dashboard
                'dashboard.view',
            ],

            'landlord' => [
                // Properties
                'properties.view',
                'properties.create',
                'properties.edit',

                // Units
                'units.view',
                'units.create',
                'units.edit',

                // Tenants
                'tenants.view',
                'tenants.create',
                'tenants.edit',

                // Leases - FULL CONTROL
                'leases.view',
                'leases.create',
                'leases.edit',
                'leases.delete',

                // Maintenance
                'maintenance.view',
                'maintenance.create',
                'maintenance.edit',

                // Payments
                'payments.view',

                // Reports
                'reports.view',

                // Dashboard
                'dashboard.view',
            ],

            'tenant' => [
                // Limited access
                'maintenance.view',
                'maintenance.create', // Tenants can create maintenance requests
                'payments.view',
                'leases.view', // Can view their own lease details only
                'units.view', // Add if tenants should see their unit details
            ],

            'vendor' => [
                // Very limited access
                'maintenance.view',
                'maintenance.edit', // Can update maintenance requests assigned to them
                'units.view', // Add if vendors need unit access for maintenance
            ],
        ];
    }

    /**
     * Get permissions for a specific role
     */
    public static function getPermissionsForRole(string $role): array
    {
        $permissions = self::getPermissions();
        return $permissions[$role] ?? [];
    }

    /**
     * Check if a role has a specific permission
     */
    public static function roleHasPermission(string $role, string $permission): bool
    {
        $rolePermissions = self::getPermissionsForRole($role);
        return in_array($permission, $rolePermissions);
    }

    /**
     * Get all available roles
     */
    public static function getAllRoles(): array
    {
        return [
            'admin' => 'Administrator',
            'manager' => 'Property Manager',
            'landlord' => 'Landlord',
            'tenant' => 'Tenant',
            'vendor' => 'Vendor',
        ];
    }

    /**
     * Get role display name
     */
    public static function getRoleDisplayName(string $role): string
    {
        $roles = self::getAllRoles();
        return $roles[$role] ?? ucfirst($role);
    }

    /**
     * Check if user has permission to manage leases
     */
    public static function canManageLeases(string $role): bool
    {
        return in_array($role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Check if user can create leases
     */
    public static function canCreateLeases(string $role): bool
    {
        return self::roleHasPermission($role, 'leases.create');
    }

    /**
     * Check if user can edit leases
     */
    public static function canEditLeases(string $role): bool
    {
        return self::roleHasPermission($role, 'leases.edit');
    }

    /**
     * Check if user can delete leases
     */
    public static function canDeleteLeases(string $role): bool
    {
        return self::roleHasPermission($role, 'leases.delete');
    }

    /**
     * Check if user can view lease details
     */
    public static function canViewLeases(string $role): bool
    {
        return self::roleHasPermission($role, 'leases.view');
    }

    /**
     * Check if user has a specific permission (instance method)
     */
    public function hasPermission($user, string $permission): bool
    {
        return self::roleHasPermission($user->role, $permission);
    }

    /**
     * Check if user can manage properties (legacy method)
     */
    public function canManageProperties($user): bool
    {
        return self::roleHasPermission($user->role, 'properties.edit');
    }

    /**
     * Check if user can create maintenance requests
     */
    public function canCreateMaintenance($user): bool
    {
        return self::roleHasPermission($user->role, 'maintenance.create');
    }

    /**
     * Check if the given user has the 'tenant' role
     */
    public function isTenant($user)
    {
        // Adjust this logic based on how roles are stored in your User model
        return $user->role === 'tenant';
    }
}
