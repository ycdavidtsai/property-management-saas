<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertyManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Create test organization
        $organization = Organization::factory()->create([
            'name' => 'Demo Property Management'
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'admin@demo.com',
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        // Create sample properties
        $properties = Property::factory()->count(3)->create([
            'organization_id' => $organization->id
        ]);

        // Create units for each property
        foreach ($properties as $property) {
            Unit::factory()->count(rand(5, 15))->create([
                'property_id' => $property->id
            ]);
        }
    }
}
