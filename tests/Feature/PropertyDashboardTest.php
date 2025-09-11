<?php

namespace Tests\Feature;

use App\Livewire\Properties\Dashboard;
use App\Models\Organization;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_property_metrics()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id
        ]);

        $property = Property::factory()->create([
            'organization_id' => $organization->id
        ]);

        Unit::factory()->count(5)->create([
            'property_id' => $property->id,
            'status' => 'occupied',
            'rent_amount' => 1000
        ]);

        Unit::factory()->count(2)->create([
            'property_id' => $property->id,
            'status' => 'vacant',
            'rent_amount' => 1000
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertViewIs('livewire.properties.dashboard')
            ->assertSee('7') // Total units
            ->assertSee('5') // Occupied units
            ->assertSee('71.4%') // Occupancy rate
            ->assertSee('7,000'); // Monthly revenue
    }

    public function test_property_selection_works()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id
        ]);

        $property = Property::factory()->create([
            'organization_id' => $organization->id
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('selectProperty', $property->id)
            ->assertSet('selectedProperty.id', $property->id)
            ->assertDispatched('property-selected');
    }
}
