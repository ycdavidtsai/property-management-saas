<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function registerWithOrganization(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create organization
            $organization = Organization::create([
                'name' => $data['organization_name'],
                'subscription_tier' => 'starter',
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
            ]);

            // Create landlord user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null, // ensure phone saved
                'password' => Hash::make($data['password']),
                'organization_id' => $organization->id,
                'role' => 'landlord',
            ]);

            return $user;
        });
    }
}
