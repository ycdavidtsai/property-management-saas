<?php

namespace App\Services;

use App\Models\BroadcastMessage;
use App\Models\User;
use App\Jobs\SendBroadcastMessageJob;
use Illuminate\Support\Collection;

class BroadcastService
{
    protected NotificationService $notificationService;

    // SMS segment constants (Twilio standards)
    const SMS_SINGLE_SEGMENT_LIMIT = 160;       // Max chars for 1 segment
    const SMS_CONCAT_SEGMENT_SIZE = 153;        // Chars per segment when concatenated
    const SMS_UNICODE_SINGLE_LIMIT = 70;        // Max chars for 1 segment with unicode
    const SMS_UNICODE_CONCAT_SIZE = 67;         // Chars per segment when concatenated with unicode

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Calculate SMS segments required for a message
     *
     * @param string $message The message content
     * @return int Number of segments
     */
    public function calculateSmsSegments(string $message): int
    {
        $length = strlen($message);

        if ($length === 0) {
            return 0;
        }

        // Check if message contains non-GSM characters (unicode)
        $isUnicode = $this->containsUnicodeCharacters($message);

        if ($isUnicode) {
            // Unicode encoding: 70 chars for single, 67 for concatenated
            if ($length <= self::SMS_UNICODE_SINGLE_LIMIT) {
                return 1;
            }
            return (int) ceil($length / self::SMS_UNICODE_CONCAT_SIZE);
        } else {
            // GSM-7 encoding: 160 chars for single, 153 for concatenated
            if ($length <= self::SMS_SINGLE_SEGMENT_LIMIT) {
                return 1;
            }
            return (int) ceil($length / self::SMS_CONCAT_SEGMENT_SIZE);
        }
    }

    /**
     * Check if message contains unicode characters (non-GSM-7)
     *
     * @param string $message
     * @return bool
     */
    protected function containsUnicodeCharacters(string $message): bool
    {
        // GSM-7 character set (basic + extended)
        // If any character is outside this set, it's unicode
        $gsm7Pattern = '/^[@£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !"#¤%&\'()*+,\-.\/:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà\f^{}\\\[~\]|€]*$/u';

        return !preg_match($gsm7Pattern, $message);
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

        // Calculate SMS segments if SMS channel is selected
        $smsSegmentsPerMessage = null;
        $smsSegmentsTotal = null;

        if (in_array('sms', $channels)) {
            $smsSegmentsPerMessage = $this->calculateSmsSegments($message);

            // Count recipients with phone numbers (only they will receive SMS)
            $recipientsWithPhone = $recipients->filter(fn($user) => !empty($user->phone))->count();
            $smsSegmentsTotal = $smsSegmentsPerMessage * $recipientsWithPhone;
        }

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
            'sms_segments_per_message' => $smsSegmentsPerMessage,
            'sms_segments_total' => $smsSegmentsTotal,
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
     * Update the actual segments total after broadcast completes
     * Call this when all jobs are processed to get accurate count
     */
    public function updateActualSegments(BroadcastMessage $broadcast): void
    {
        if ($broadcast->sms_segments_per_message && $broadcast->sms_sent > 0) {
            $broadcast->update([
                'sms_segments_total' => $broadcast->sms_segments_per_message * $broadcast->sms_sent
            ]);
        }
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
            'sms_segments_per_message' => $broadcast->sms_segments_per_message,
            'sms_segments_total' => $broadcast->sms_segments_total,
            'delivery_rate' => $broadcast->delivery_rate,
            'total_sent' => $broadcast->total_sent,
            'total_delivered' => $broadcast->total_delivered,
            'total_failed' => $broadcast->total_failed,
        ];
    }

    /**
     * Get SMS usage for an organization within a date range
     * Useful for billing and usage reports
     */
    public function getSmsUsage(string $organizationId, ?\DateTime $from = null, ?\DateTime $to = null): array
    {
        $query = BroadcastMessage::where('organization_id', $organizationId)
            ->whereNotNull('sms_segments_total')
            ->where('sms_segments_total', '>', 0);

        if ($from) {
            $query->where('sent_at', '>=', $from);
        }

        if ($to) {
            $query->where('sent_at', '<=', $to);
        }

        $broadcasts = $query->get();

        return [
            'total_broadcasts' => $broadcasts->count(),
            'total_sms_sent' => $broadcasts->sum('sms_sent'),
            'total_segments' => $broadcasts->sum('sms_segments_total'),
            'period_start' => $from?->format('Y-m-d'),
            'period_end' => $to?->format('Y-m-d'),
        ];
    }

    /**
     * Get current month's SMS usage for an organization
     */
    public function getCurrentMonthSmsUsage(string $organizationId): array
    {
        return $this->getSmsUsage(
            $organizationId,
            now()->startOfMonth()->toDateTime(),
            now()->endOfMonth()->toDateTime()
        );
    }
}
