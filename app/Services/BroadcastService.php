<?php

namespace App\Services;

use App\Models\BroadcastMessage;
use App\Models\User;
use App\Jobs\SendBroadcastMessageJob;
use Illuminate\Support\Collection;

class BroadcastService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create and optionally send a broadcast message
     */
    public function createBroadcast(
        User $sender,
        string $title,
        string $message,
        array $channels,
        string $recipientType,
        ?array $recipientFilters = null,
        ?\DateTime $scheduledAt = null
    ): BroadcastMessage {
        $recipients = $this->getRecipients($sender->organization_id, $recipientType, $recipientFilters);

        $broadcast = BroadcastMessage::create([
            'organization_id' => $sender->organization_id,
            'sender_id' => $sender->id,
            'title' => $title,
            'message' => $message,
            'channels' => $channels,
            'recipient_type' => $recipientType,
            'recipient_filters' => $recipientFilters,
            'recipient_count' => $recipients->count(),
            'status' => $scheduledAt ? 'scheduled' : 'draft',
            'scheduled_at' => $scheduledAt,
        ]);

        // If not scheduled, send immediately
        if (!$scheduledAt) {
            $this->sendBroadcast($broadcast, $recipients);
        }

        return $broadcast;
    }

    /**
     * Get recipients based on filters
     */
    public function getRecipients(
        string $organizationId,
        string $recipientType,
        ?array $filters = null
    ): Collection {
        $query = User::where('organization_id', $organizationId)
            ->where('role', 'tenant')
            ->whereNotNull('email');

        switch ($recipientType) {
            case 'property':
                if (isset($filters['property_ids'])) {
                    $query->whereHas('leases.unit', function ($q) use ($filters) {
                        $q->whereIn('property_id', $filters['property_ids'])
                          ->where('leases.status', 'active');
                    });
                }
                break;

            case 'unit':
                if (isset($filters['unit_ids'])) {
                    $query->whereHas('leases', function ($q) use ($filters) {
                        $q->whereIn('unit_id', $filters['unit_ids'])
                          ->where('status', 'active');
                    });
                }
                break;

            case 'specific_users':
                if (isset($filters['user_ids'])) {
                    $query->whereIn('id', $filters['user_ids']);
                }
                break;

            case 'all_tenants':
            default:
                // Get all tenants with active leases
                $query->whereHas('leases', function ($q) {
                    $q->where('status', 'active');
                });
                break;
        }

        return $query->get();
    }

    /**
     * Send broadcast to recipients via queue
     */
    public function sendBroadcast(BroadcastMessage $broadcast, Collection $recipients): void
    {
        $broadcast->markAsSending();

        // Dispatch jobs for each recipient
        foreach ($recipients as $recipient) {
            SendBroadcastMessageJob::dispatch($broadcast, $recipient);
        }

        $broadcast->markAsSent();
    }

    /**
     * Preview recipients without sending
     */
    public function previewRecipients(
        string $organizationId,
        string $recipientType,
        ?array $recipientFilters = null
    ): array {
        $recipients = $this->getRecipients($organizationId, $recipientType, $recipientFilters);

        return [
            'count' => $recipients->count(),
            'recipients' => $recipients->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'unit' => $user->leases()->where('status', 'active')->first()?->unit?->unit_number,
                    'property' => $user->leases()->where('status', 'active')->first()?->unit?->property?->name,
                ];
            })->values()->all()
        ];
    }

    /**
     * Get broadcast statistics
     */
    public function getBroadcastStats(BroadcastMessage $broadcast): array
    {
        return [
            'total_recipients' => $broadcast->recipient_count,
            'emails_sent' => $broadcast->emails_sent,
            'emails_delivered' => $broadcast->emails_delivered,
            'emails_failed' => $broadcast->emails_failed,
            'sms_sent' => $broadcast->sms_sent,
            'sms_delivered' => $broadcast->sms_delivered,
            'sms_failed' => $broadcast->sms_failed,
            'delivery_rate' => $broadcast->delivery_rate,
            'total_sent' => $broadcast->total_sent,
            'total_delivered' => $broadcast->total_delivered,
            'total_failed' => $broadcast->total_failed,
        ];
    }
}
