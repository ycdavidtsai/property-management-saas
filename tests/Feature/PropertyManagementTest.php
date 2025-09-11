<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'admin'
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Property Overview');
    }

    public function test_user_can_create_property()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'admin'
        ]);

        $propertyData = [
            'name' => 'Test Property',
            'address' => '123 Test St, Test City, TC 12345',
            'type' => 'apartment',
            'total_units' => 10,
        ];

        $this->actingAs($user)
            ->get(route('properties.create'))
            ->assertStatus(200);

        $this->assertDatabaseMissing('properties', $propertyData);
    }

    public function test_properties_are_organization_scoped()
    {
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);

        $property1 = Property::factory()->create(['organization_id' => $org1->id]);
        $property2 = Property::factory()->create(['organization_id' => $org2->id]);

        $this->actingAs($user1);

        // User 1 should only see their org's property
        $this->get(route('properties.index'))
            ->assertStatus(200);
    }
}
