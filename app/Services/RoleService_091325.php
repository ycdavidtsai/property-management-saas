<?php

namespace App\Services;

class RoleService
{
    public const TENANT_PERMISSIONS = [
        'maintenance.view',
        'payments.view',
    ];

    public const LANDLORD_PERMISSIONS = [
        'properties.view', 'properties.create', 'properties.edit',
        'units.view', 'units.create', 'units.edit',
        'tenants.view', 'tenants.create', 'tenants.edit',
        'leases.view', 'leases.create', 'leases.edit',
        'maintenance.view', 'maintenance.assign',
        'payments.view', 'payments.process',
        'reports.view', 'communications.send'
    ];

    public static function getPermissionsForRole(string $role): array
    {
        return match($role) {
            'tenant' => self::TENANT_PERMISSIONS,
            'landlord' => self::LANDLORD_PERMISSIONS,
            'admin', 'manager' => [], // Admin/Manager get all permissions by default
            'vendor' => ['maintenance.view', 'maintenance.complete'],
            default => []
        };
    }
}
