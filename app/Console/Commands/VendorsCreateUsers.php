<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorsCreateUsers extends Command
{
    protected $signature = 'vendors:create-users {--force : Create accounts without confirmation}';
    protected $description = 'Create user accounts for vendors that don\'t have portal access';

    public function handle()
    {
        $vendorsWithoutUsers = Vendor::whereNull('user_id')
            ->where('is_active', true)
            ->get();

        if ($vendorsWithoutUsers->isEmpty()) {
            $this->info('All active vendors already have user accounts.');
            return 0;
        }

        $this->info("Found {$vendorsWithoutUsers->count()} active vendors without user accounts:");

        foreach ($vendorsWithoutUsers as $vendor) {
            $this->line("- {$vendor->name} ({$vendor->email})");
        }

        if (!$this->option('force') && !$this->confirm('Create user accounts for these vendors?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $created = 0;
        $skipped = 0;

        foreach ($vendorsWithoutUsers as $vendor) {
            // Check if email is already taken
            $existingUser = User::where('email', $vendor->email)->first();

            if ($existingUser) {
                $this->warn("Skipped {$vendor->name} - Email {$vendor->email} already in use");
                $skipped++;
                continue;
            }

            // Create user account
            $temporaryPassword = Str::random(16);

            $user = User::create([
                'name' => $vendor->name,
                'email' => $vendor->email,
                'password' => Hash::make($temporaryPassword),
                'organization_id' => $vendor->organization_id,
                'role' => 'vendor',
            ]);

            // Link vendor to user
            $vendor->update(['user_id' => $user->id]);

            $this->info("✓ Created account for {$vendor->name}");
            $created++;
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("- Created: {$created}");
        $this->info("- Skipped: {$skipped}");
        $this->newLine();
        $this->warn("IMPORTANT: Send password reset links to all new vendor users!");

        return 0;
    }
}

// To use this command:
// php artisan vendors:create-users
// Or without confirmation:
// php artisan vendors:create-users --force
