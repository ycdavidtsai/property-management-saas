<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    /**
     * Determine if user can view any vendors
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if user can view vendor
     */
    public function view(User $user, Vendor $vendor): bool
    {
        // Admins can view all
        if ($user->role === 'admin') {
            return true;
        }

        // Check if vendor is visible to user's organization
        return $vendor->isGlobal() || $vendor->isOwnedBy($user->organization_id);
    }

    /**
     * Determine if user can create vendors
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'landlord']);
    }

    /**
     * Determine if user can update vendor
     */
    public function update(User $user, Vendor $vendor): bool
    {
        return $vendor->canBeEditedBy($user);
    }

    /**
     * Determine if user can delete vendor
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        // Admins can delete any vendor
        if ($user->role === 'admin') {
            return true;
        }

        // Organizations can only delete their own private vendors
        return $vendor->isPrivate() && $vendor->isOwnedBy($user->organization_id);
    }
}
