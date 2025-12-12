<?php

namespace App\Jobs;

use App\Models\Vendor;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVendorInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(
        public Vendor $vendor,
        public string $message,
        public ?User $sentBy = null
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        try {
            // Send SMS directly (not creating notification record)
            $notificationService->sendSmsDirectly(
                $this->vendor->phone,
                $this->message
            );

            Log::info('Vendor invitation SMS sent', [
                'vendor_id' => $this->vendor->id,
                'phone' => $this->vendor->phone,
            ]);

        } catch (\Exception $e) {
            Log::error('Vendor invitation SMS failed', [
                'vendor_id' => $this->vendor->id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Vendor invitation job permanently failed', [
            'vendor_id' => $this->vendor->id,
            'error' => $exception->getMessage(),
        ]);

        // Optionally notify admin of failed invitation
    }
}

