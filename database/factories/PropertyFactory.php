<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->words(2, true) . ' Apartments',
            'address' => $this->faker->address(),
            'type' => $this->faker->randomElement(['single_family', 'multi_family', 'apartment', 'commercial']),
            'total_units' => $this->faker->numberBetween(1, 50),
        ];
    }
}
