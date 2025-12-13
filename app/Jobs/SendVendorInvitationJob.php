<?php

namespace App\Jobs;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class SendVendorInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s

    public function __construct(
        public Vendor $vendor,
        public string $message,
        public ?User $sentBy = null
    ) {}

    public function handle(): void
    {
        try {
            // Check Twilio configuration
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $fromPhone = config('services.twilio.phone');

            if (!$sid || !$token || !$fromPhone) {
                Log::error('Vendor invitation SMS failed - Twilio not configured', [
                    'vendor_id' => $this->vendor->id,
                ]);
                throw new \Exception('Twilio SMS service not configured');
            }

            // Format phone number
            $toPhone = $this->formatPhoneNumber($this->vendor->phone);

            // Create Twilio client fresh in job context
            $twilio = new TwilioClient($sid, $token);

            // Send SMS WITHOUT statusCallback to avoid route resolution issues
            // For invitation SMS, we don't need delivery tracking - we care about send success
            $twilioMessage = $twilio->messages->create(
                $toPhone,
                [
                    'from' => $fromPhone,
                    'body' => $this->message,
                    // Note: No statusCallback - avoids route resolution issues in queued jobs
                ]
            );

            Log::info('Vendor invitation SMS sent successfully', [
                'vendor_id' => $this->vendor->id,
                'phone' => $toPhone,
                'twilio_sid' => $twilioMessage->sid,
                'twilio_status' => $twilioMessage->status,
            ]);

        } catch (\Twilio\Exceptions\TwilioException $e) {
            Log::error('Vendor invitation SMS failed - Twilio error', [
                'vendor_id' => $this->vendor->id,
                'phone' => $this->vendor->phone,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw $e; // Re-throw to trigger retry
        } catch (\Exception $e) {
            Log::error('Vendor invitation SMS failed', [
                'vendor_id' => $this->vendor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // If 10 digits, assume US number
        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }

        // If 11 digits starting with 1, assume US number
        if (strlen($digits) === 11 && $digits[0] === '1') {
            return '+' . $digits;
        }

        // If already has +, return as-is
        if (str_starts_with($phone, '+')) {
            return '+' . $digits;
        }

        // Default: add + prefix
        return '+' . $digits;
    }

    /**
     * Handle job failure after all retries exhausted
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Vendor invitation job permanently failed after all retries', [
            'vendor_id' => $this->vendor->id,
            'phone' => $this->vendor->phone,
            'error' => $exception->getMessage(),
        ]);

        // Optionally: Update vendor record to indicate invitation failed
        // $this->vendor->update(['invitation_status' => 'failed']);

        // Optionally: Notify admin of failed invitation
        // Could send email to admin here
    }
}
