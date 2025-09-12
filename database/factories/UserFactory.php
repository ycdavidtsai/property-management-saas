<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // database/factories/UserFactory.php - Add these methods
    public function landlord()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'landlord',
                'permissions' => [
                    'properties.view', 'properties.create', 'properties.edit',
                    'units.view', 'units.create', 'units.edit',
                    'tenants.view', 'tenants.create', 'tenants.edit',
                    'leases.view', 'leases.create', 'leases.edit',
                    'maintenance.view', 'maintenance.assign',
                    'payments.view', 'reports.view'
                ]
            ];
        });
    }

    public function tenant()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'tenant',
                'permissions' => [
                    'maintenance.view',
                    'payments.view'
                ]
            ];
        });
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
                'permissions' => [] // Admin gets all permissions by default
            ];
        });
    }

    public function vendor()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'vendor',
                'permissions' => [
                    'maintenance.view',
                    'maintenance.complete'
                ]
            ];
        });
    }
}
