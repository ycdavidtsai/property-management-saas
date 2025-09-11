<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'unit_number' => $this->faker->unique()->numberBetween(100, 999),
            'bedrooms' => $this->faker->numberBetween(1, 4),
            'bathrooms' => $this->faker->randomElement([1, 1.5, 2, 2.5, 3]),
            'square_feet' => $this->faker->numberBetween(500, 2000),
            'rent_amount' => $this->faker->numberBetween(800, 3000),
            'status' => $this->faker->randomElement(['vacant', 'occupied', 'maintenance']),
        ];
    }
}
