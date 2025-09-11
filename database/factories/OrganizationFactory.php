<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'subscription_tier' => 'starter',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ];
    }
}
