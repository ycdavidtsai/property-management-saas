<?php

namespace App\Jobs;

use App\Models\BroadcastMessage;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBroadcastMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public BroadcastMessage $broadcast,
        public User $recipient
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        try {
            $results = $notificationService->send(
                $this->recipient,
                $this->broadcast->title,
                $this->broadcast->message,
                $this->broadcast->channels,
                'broadcast',
                $this->broadcast,
                $this->broadcast->sender
            );

            // Update broadcast statistics based on results
            foreach ($results as $channel => $notification) {
                if ($notification->status === 'sent') {
                    if ($channel === 'email') {
                        $this->broadcast->increment('emails_sent');
                    } elseif ($channel === 'sms') {
                        $this->broadcast->increment('sms_sent');
                    }
                } elseif ($notification->status === 'failed') {
                    if ($channel === 'email') {
                        $this->broadcast->increment('emails_failed');
                    } elseif ($channel === 'sms') {
                        $this->broadcast->increment('sms_failed');
                    }
                }
            }

            Log::info('Broadcast message sent to recipient', [
                'broadcast_id' => $this->broadcast->id,
                'recipient_id' => $this->recipient->id,
                'channels' => $this->broadcast->channels,
            ]);

        } catch (\Exception $e) {
            Log::error('Broadcast message job failed', [
                'broadcast_id' => $this->broadcast->id,
                'recipient_id' => $this->recipient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Increment failed counters
            foreach ($this->broadcast->channels as $channel) {
                if ($channel === 'email') {
                    $this->broadcast->increment('emails_failed');
                } elseif ($channel === 'sms') {
                    $this->broadcast->increment('sms_failed');
                }
            }

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Broadcast message job permanently failed', [
            'broadcast_id' => $this->broadcast->id,
            'recipient_id' => $this->recipient->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
