<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotification;
use Twilio\Rest\Client as TwilioClient;

class NotificationService
{
    protected ?TwilioClient $twilio = null;

    public function __construct()
    {
        // Only initialize Twilio if credentials are configured
        if (config('services.twilio.sid') && config('services.twilio.token')) {
            $this->twilio = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        }
    }

    /**
     * Send notification to a user via their preferred channels
     */
    public function send(
        User $user,
        string $subject,
        string $content,
        array $channels = ['email'],
        string $notificationChannel = 'general',
        $notifiable = null,
        ?User $fromUser = null
    ): array {
        $results = [];

        foreach ($channels as $channel) {
            if ($channel === 'email' && $user->email) {
                $results['email'] = $this->sendEmail(
                    $user,
                    $subject,
                    $content,
                    $notificationChannel,
                    $notifiable,
                    $fromUser
                );
            }

            if ($channel === 'sms' && $user->phone) {
                $results['sms'] = $this->sendSMS(
                    $user,
                    $content,
                    $notificationChannel,
                    $notifiable,
                    $fromUser
                );
            }
        }

        return $results;
    }

    /**
     * Send email notification
     */
    public function sendEmail(
        User $user,
        string $subject,
        string $content,
        string $channel = 'general',
        $notifiable = null,
        ?User $fromUser = null
    ): Notification {
        $notification = Notification::create([
            'organization_id' => $user->organization_id,
            'from_user_id' => $fromUser?->id,
            'to_user_id' => $user->id,
            'type' => 'email',
            'channel' => $channel,
            'subject' => $subject,
            'content' => $content,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'status' => 'pending',
        ]);

        try {
            Mail::to($user->email)->send(new GenericNotification(
                $subject,
                $content,
                $notification
            ));

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info('Email notification sent', [
                'notification_id' => $notification->id,
                'to' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Email notification failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $notification;
    }

    /**
     * Send SMS notification
     */
    public function sendSMS(
        User $user,
        string $content,
        string $channel = 'general',
        $notifiable = null,
        ?User $fromUser = null
    ): Notification {
        $notification = Notification::create([
            'organization_id' => $user->organization_id,
            'from_user_id' => $fromUser?->id,
            'to_user_id' => $user->id,
            'type' => 'sms',
            'channel' => $channel,
            'content' => $content,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'status' => 'pending',
        ]);

        if (!$this->twilio) {
            $notification->update([
                'status' => 'failed',
                'error_message' => 'Twilio not configured',
            ]);
            return $notification;
        }

        try {
            $message = $this->twilio->messages->create(
                $user->phone,
                [
                    'from' => config('services.twilio.phone'),
                    'body' => $content,
                    'statusCallback' => route('webhooks.twilio.status'),
                ]
            );

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider_id' => $message->sid,
                'provider_response' => [
                    'status' => $message->status,
                    'date_sent' => $message->dateSent?->format('Y-m-d H:i:s'),
                ],
            ]);

            Log::info('SMS notification sent', [
                'notification_id' => $notification->id,
                'to' => $user->phone,
                'provider_id' => $message->sid
            ]);
        } catch (\Exception $e) {
            Log::error('SMS notification failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $notification;
    }

    /**
     * Send maintenance request notification based on event
     */
    public function sendMaintenanceNotification(
        $maintenanceRequest,
        string $event,
        ?User $recipient = null
    ): void {
        $templates = [
            'created' => [
                'subject' => 'New Maintenance Request Submitted',
                'email' => "Your maintenance request has been submitted successfully.\n\nCategory: {category}\nPriority: {priority}\nDescription: {description}",
                'sms' => 'Your maintenance request #{id} has been submitted and will be reviewed shortly.',
            ],
            'assigned' => [
                'subject' => 'Maintenance Request Assigned',
                'email' => "Your maintenance request has been assigned to a vendor.\n\nVendor: {vendor}\nScheduled: {scheduled}",
                'sms' => 'Your request #{id} has been assigned to {vendor}.',
            ],
            'in_progress' => [
                'subject' => 'Work Started on Your Request',
                'email' => "The vendor has started working on your maintenance request.\n\nVendor: {vendor}",
                'sms' => 'Work has started on request #{id}.',
            ],
            'completed' => [
                'subject' => 'Maintenance Request Completed',
                'email' => "Your maintenance request has been completed.\n\nCompleted on: {completed_at}\nVendor: {vendor}",
                'sms' => 'Your request #{id} is now completed.',
            ],
        ];

        $template = $templates[$event] ?? null;
        if (!$template) {
            return;
        }

        // Determine recipient
        $user = $recipient ?? $maintenanceRequest->tenant;

        // Replace placeholders
        $replacements = [
            '{id}' => substr($maintenanceRequest->id, 0, 8),
            '{category}' => $maintenanceRequest->category ?? 'N/A',
            '{priority}' => ucfirst($maintenanceRequest->priority ?? 'N/A'),
            '{description}' => $maintenanceRequest->description ?? 'N/A',
            '{vendor}' => $maintenanceRequest->assignedVendor?->company_name ?? 'Vendor',
            '{scheduled}' => $maintenanceRequest->scheduled_date?->format('M j, Y') ?? 'TBD',
            '{completed_at}' => $maintenanceRequest->completed_at?->format('M j, Y') ?? now()->format('M j, Y'),
        ];

        $emailContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template['email']
        );

        $smsContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template['sms']
        );

        $this->send(
            $user,
            $template['subject'],
            $emailContent,
            ['email', 'sms'],
            'maintenance',
            $maintenanceRequest
        );
    }
}
