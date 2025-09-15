<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Lease;
use Carbon\Carbon;

class LeaseTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run if we have existing data
        if (!User::where('role', 'tenant')->exists()) {
            $this->command->warn('No tenant users found. Please create some tenants first.');
            return;
        }

        if (!Unit::exists()) {
            $this->command->warn('No units found. Please create some properties and units first.');
            return;
        }

        $this->command->info('Creating test lease data...');

        // Get some test data
        $tenants = User::where('role', 'tenant')->take(6)->get();
        $units = Unit::whereIn('status', ['vacant', 'for_lease'])->take(4)->get();

        if ($tenants->isEmpty() || $units->isEmpty()) {
            $this->command->warn('Insufficient test data. Need at least 6 tenants and 4 vacant units.');
            return;
        }

        $organizationId = $tenants->first()->organization_id;

        // Create test leases with different statuses
        $leases = [
            [
                'unit' => $units[0],
                'tenants' => [$tenants[0], $tenants[1]],
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6), // Active lease
                'status' => 'active',
                'notes' => 'Test active lease with two tenants'
            ],
            [
                'unit' => $units[1],
                'tenants' => [$tenants[2]],
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addDays(30), // Expiring soon
                'status' => 'expiring_soon',
                'notes' => 'Test lease expiring in 30 days'
            ],
            [
                'unit' => $units[2],
                'tenants' => [$tenants[3]],
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->subDays(15), // Expired
                'status' => 'expired',
                'notes' => 'Test expired lease - needs attention'
            ],
            [
                'unit' => $units[3],
                'tenants' => [$tenants[4], $tenants[5]],
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9), // Active, long-term
                'status' => 'active',
                'notes' => 'Test long-term active lease'
            ],
        ];

        foreach ($leases as $leaseData) {
            // Create the lease
            $lease = Lease::create([
                'organization_id' => $organizationId,
                'unit_id' => $leaseData['unit']->id,
                'start_date' => $leaseData['start_date'],
                'end_date' => $leaseData['end_date'],
                'rent_amount' => $leaseData['unit']->rent_amount,
                'security_deposit' => $leaseData['unit']->rent_amount, // Security deposit = 1 month rent
                'status' => $leaseData['status'],
                'notes' => $leaseData['notes'],
            ]);

            // Attach tenants
            $tenantIds = collect($leaseData['tenants'])->pluck('id')->toArray();
            $lease->tenants()->attach($tenantIds);

            // Update unit status
            if (in_array($leaseData['status'], ['active', 'expiring_soon'])) {
                $leaseData['unit']->update(['status' => 'occupied']);
            }

            $this->command->info("Created lease: {$leaseData['unit']->property->name} - Unit {$leaseData['unit']->unit_number} ({$leaseData['status']})");
        }

        $this->command->info('✅ Test lease data created successfully!');
        $this->command->line('');
        $this->command->info('You can now test:');
        $this->command->line('- Visit /leases to see the test leases');
        $this->command->line('- Run: php artisan leases:update-statuses --dry-run');
        $this->command->line('- Login as different user roles to test permissions');
    }
}