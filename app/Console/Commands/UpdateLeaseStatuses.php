<?php

namespace App\Console\Commands;

use App\Models\Lease;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateLeaseStatuses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'leases:update-statuses {--dry-run : Run without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Update lease statuses based on dates (active -> expiring_soon -> expired)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🧪 DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        $this->info('🔍 Checking lease statuses...');
        
        // Get all non-terminated leases
        $leases = Lease::whereIn('status', ['active', 'expiring_soon'])->get();
        
        $this->info("Found {$leases->count()} leases to check");
        $this->line('');

        $updated = 0;
        $statusChanges = [
            'active_to_expiring' => 0,
            'active_to_expired' => 0,
            'expiring_to_expired' => 0,
        ];

        foreach ($leases as $lease) {
            $originalStatus = $lease->status;
            $newStatus = $this->determineLeaseStatus($lease);
            
            if ($originalStatus !== $newStatus) {
                $this->line("📋 Lease ID: {$lease->id}");
                $this->line("   Property: {$lease->unit->property->name} - Unit {$lease->unit->unit_number}");
                $this->line("   End Date: {$lease->end_date->format('M j, Y')}");
                $this->line("   Status: {$originalStatus} → {$newStatus}");
                
                if (!$isDryRun) {
                    $lease->update(['status' => $newStatus]);
                    $this->info("   ✅ Updated!");
                } else {
                    $this->comment("   🔍 Would update (dry run)");
                }
                
                $updated++;
                
                // Track status changes
                if ($originalStatus === 'active' && $newStatus === 'expiring_soon') {
                    $statusChanges['active_to_expiring']++;
                } elseif ($originalStatus === 'active' && $newStatus === 'expired') {
                    $statusChanges['active_to_expired']++;
                } elseif ($originalStatus === 'expiring_soon' && $newStatus === 'expired') {
                    $statusChanges['expiring_to_expired']++;
                }
                
                $this->line('');
            }
        }

        // Summary
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 SUMMARY');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        if ($updated > 0) {
            $this->info("✅ {$updated} lease(s) " . ($isDryRun ? 'would be updated' : 'updated'));
            
            if ($statusChanges['active_to_expiring'] > 0) {
                $this->line("   🟡 {$statusChanges['active_to_expiring']} lease(s): active → expiring soon");
            }
            if ($statusChanges['active_to_expired'] > 0) {
                $this->line("   🔴 {$statusChanges['active_to_expired']} lease(s): active → expired");
            }
            if ($statusChanges['expiring_to_expired'] > 0) {
                $this->line("   🔴 {$statusChanges['expiring_to_expired']} lease(s): expiring soon → expired");
            }
        } else {
            $this->info('✅ All lease statuses are up to date');
        }
        
        $this->line('');
        $this->info('🎉 Lease status update completed!');
        
        if ($isDryRun) {
            $this->comment('Run without --dry-run to apply changes');
        }

        return Command::SUCCESS;
    }

    /**
     * Determine the correct status for a lease based on dates
     */
    private function determineLeaseStatus(Lease $lease): string
    {
        $now = Carbon::now();
        $endDate = $lease->end_date;
        $sixtyDaysFromNow = $now->copy()->addDays(60);

        // If already terminated, don't change
        if ($lease->status === 'terminated') {
            return 'terminated';
        }

        // If end date has passed, it's expired
        if ($endDate->lt($now)) {
            return 'expired';
        }

        // If end date is within 60 days, it's expiring soon
        if ($endDate->lte($sixtyDaysFromNow)) {
            return 'expiring_soon';
        }

        // Otherwise, it's active
        return 'active';
    }
}