<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotification;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Http;

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
        // Filter channels based on user preferences
        $allowedChannels = $this->getAllowedChannels($user, $notificationChannel, $channels);

        // If no channels are allowed, log and return empty
        if (empty($allowedChannels)) {
            Log::info('Notification skipped - user preferences disabled all channels', [
                'user_id' => $user->id,
                'channel' => $notificationChannel,
                'requested_channels' => $channels,
            ]);
            return [];
        }

        $results = [];

        foreach ($allowedChannels as $channel) {
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
     * Get allowed notification channels based on user preferences
     */
    protected function getAllowedChannels(User $user, string $notificationChannel, array $requestedChannels): array
    {
        // If user has no preferences set, allow all requested channels (default behavior)
        if (!$user->notification_preferences) {
            return $requestedChannels;
        }

        $preferences = $user->notification_preferences;

        // If no preferences for this specific channel, allow all (default)
        if (!isset($preferences[$notificationChannel])) {
            return $requestedChannels;
        }

        $channelPrefs = $preferences[$notificationChannel];
        $allowedChannels = [];

        // Check each requested channel against user preferences
        foreach ($requestedChannels as $channel) {
            if (isset($channelPrefs[$channel]) && $channelPrefs[$channel] === true) {
                $allowedChannels[] = $channel;
            }
        }

        return $allowedChannels;
    }

    /**
     * Send email notification with Postmark MessageID capture
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
            // Check if using Postmark
            $mailDriver = config('mail.default');

            if ($mailDriver === 'postmark') {
                // Send via Postmark API directly to get MessageID
                $postmarkToken = config('services.postmark.token');

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Postmark-Server-Token' => $postmarkToken,
                ])
                ->post('https://api.postmarkapp.com/email', [
                    'From' => config('mail.from.address'),
                    'To' => $user->email,
                    'Subject' => $subject,
                    'HtmlBody' => nl2br(e($content)),
                    'TextBody' => $content,
                    'MessageStream' => 'outbound',
                    'TrackOpens' => true,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $messageId = $data['MessageID'] ?? null;

                    $notification->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'provider_id' => $messageId, // Store Postmark MessageID
                        'provider_response' => [
                            'to' => $data['To'] ?? null,
                            'submitted_at' => $data['SubmittedAt'] ?? null,
                        ],
                    ]);

                    Log::info('Email notification sent via Postmark', [
                        'notification_id' => $notification->id,
                        'to' => $user->email,
                        'message_id' => $messageId,
                    ]);
                } else {
                    throw new \Exception('Postmark API error: ' . $response->body());
                }
            } else {
                // Fall back to standard Laravel Mail for other drivers
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
                    'to' => $user->email,
                    'driver' => $mailDriver,
                ]);
            }
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
        // Check if user has a phone number
        if (!$user->phone) {
            $notification = Notification::create([
                'organization_id' => $user->organization_id,
                'from_user_id' => $fromUser?->id,
                'to_user_id' => $user->id,
                'type' => 'sms',
                'channel' => $channel,
                'content' => $content,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable?->id,
                'status' => 'failed',
                'error_message' => 'User does not have a phone number',
            ]);

            Log::warning('SMS notification skipped - no phone number', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ]);

            return $notification;
        }

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
            // Build message parameters
            $messageParams = [
                'from' => config('services.twilio.phone'),
                'body' => $content,
            ];

            // Only add statusCallback if we have a valid public URL (not localhost)
            $appUrl = config('app.url');
            if ($appUrl && !str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
                $messageParams['statusCallback'] = url('/twilio-webhook.php'); // Direct file bypasses CSRF
            }

            $message = $this->twilio->messages->create(
                $user->phone,
                $messageParams
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
     * ✨ NEW: Send maintenance request notification to multiple recipients based on event
     */
    public function sendMaintenanceNotification(
        $maintenanceRequest,
        string $event,
        ?User $specificRecipient = null
    ): void {
        // If specific recipient provided, use old behavior (single notification)
        if ($specificRecipient) {
            $this->sendMaintenanceNotificationToUser($maintenanceRequest, $event, $specificRecipient);
            return;
        }

        // ✨ NEW: Send to multiple recipients based on event type
        switch ($event) {
            case 'created':
                // Notify tenant (confirmation)
                $this->sendMaintenanceNotificationToUser(
                    $maintenanceRequest,
                    'created_tenant',
                    $maintenanceRequest->tenant
                );

                // ✨ NEW: Notify landlord/manager (new request alert)
                $this->notifyLandlordsAndManagers(
                    $maintenanceRequest,
                    'created_landlord'
                );
                break;

            case 'assigned':
                // Notify tenant (vendor assigned)
                $this->sendMaintenanceNotificationToUser(
                    $maintenanceRequest,
                    'assigned_tenant',
                    $maintenanceRequest->tenant
                );

                // ✨ FIX: Notify vendor directly (vendors may not have organization_id)
                if ($maintenanceRequest->assignedVendor) {
                    $this->notifyVendorOfAssignment($maintenanceRequest);
                }
                break;

            case 'in_progress':
                // Notify tenant
                $this->sendMaintenanceNotificationToUser(
                    $maintenanceRequest,
                    'in_progress_tenant',
                    $maintenanceRequest->tenant
                );
                break;

            case 'completed':
                // Notify tenant
                $this->sendMaintenanceNotificationToUser(
                    $maintenanceRequest,
                    'completed_tenant',
                    $maintenanceRequest->tenant
                );

                // ✨ NEW: Notify landlord/manager (completion confirmation)
                $this->notifyLandlordsAndManagers(
                    $maintenanceRequest,
                    'completed_landlord'
                );
                break;
        }
    }

    /**
     * ✨ FIX: Notify vendor directly (handles vendors without organization_id)
     */
    protected function notifyVendorOfAssignment($maintenanceRequest): void
    {
        $vendor = $maintenanceRequest->assignedVendor;

        if (!$vendor) {
            Log::warning('No vendor found for assignment notification', [
                'maintenance_request_id' => $maintenanceRequest->id,
            ]);
            return;
        }

        // Get vendor's user account OR use vendor's direct contact info
        $vendorUser = $vendor->user ?? null;

        // Build notification content
        $replacements = [
            '{id}' => substr($maintenanceRequest->id, 0, 8),
            '{category}' => $maintenanceRequest->category ?? 'N/A',
            '{priority}' => ucfirst($maintenanceRequest->priority ?? 'N/A'),
            '{description}' => $maintenanceRequest->description ?? 'N/A',
            '{scheduled}' => $maintenanceRequest->scheduled_date?->format('M j, Y') ?? 'TBD',
            '{property}' => $maintenanceRequest->unit?->property?->name ?? 'Property',
            '{unit}' => $maintenanceRequest->unit?->unit_number ?? 'N/A',
            '{tenant}' => $maintenanceRequest->tenant?->name ?? 'Tenant',
            '{tenant_phone}' => $maintenanceRequest->tenant?->phone ?? 'N/A',
        ];

        $emailTemplate = "You have been assigned a new maintenance job.\n\nProperty: {property}\nUnit: {unit}\nTenant: {tenant}\nTenant Phone: {tenant_phone}\n\nCategory: {category}\nPriority: {priority}\nDescription: {description}\n\nScheduled: {scheduled}\n\nPlease contact the tenant to coordinate access.";

        $smsTemplate = 'New {priority} job assigned: {category} at {property} Unit {unit}. Contact tenant at {tenant_phone}.';

        $emailContent = str_replace(array_keys($replacements), array_values($replacements), $emailTemplate);
        $smsContent = str_replace(array_keys($replacements), array_values($replacements), $smsTemplate);

        // Get organization_id from the maintenance request for notification record
        $organizationId = $maintenanceRequest->unit?->property?->organization_id;

        // If vendor has a user account, try to use it
        if ($vendorUser) {
            // Check if vendor user has organization_id
            if ($vendorUser->organization_id) {
                // Vendor has proper organization setup, use normal flow
                $this->sendMaintenanceNotificationToUser(
                    $maintenanceRequest,
                    'assigned_vendor',
                    $vendorUser
                );
                return;
            }

            // ✨ FIX: Vendor user exists but no organization_id
            // Send using the maintenance request's organization_id
            try {
                // Send Email
                if ($vendorUser->email) {
                    $notification = Notification::create([
                        'organization_id' => $organizationId, // ✅ Use request's organization
                        'from_user_id' => null,
                        'to_user_id' => $vendorUser->id,
                        'type' => 'email',
                        'channel' => 'maintenance',
                        'subject' => 'New Maintenance Job Assigned',
                        'content' => $emailContent,
                        'notifiable_type' => get_class($maintenanceRequest),
                        'notifiable_id' => $maintenanceRequest->id,
                        'status' => 'pending',
                    ]);

                    Mail::to($vendorUser->email)->send(new GenericNotification(
                        'New Maintenance Job Assigned',
                        $emailContent,
                        $notification
                    ));

                    $notification->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);

                    Log::info('Vendor email notification sent', [
                        'vendor_id' => $vendor->id,
                        'vendor_user_id' => $vendorUser->id,
                        'notification_id' => $notification->id,
                    ]);
                }

                // Send SMS
                if ($vendorUser->phone && $this->twilio) {
                    $notification = Notification::create([
                        'organization_id' => $organizationId, // ✅ Use request's organization
                        'from_user_id' => null,
                        'to_user_id' => $vendorUser->id,
                        'type' => 'sms',
                        'channel' => 'maintenance',
                        'content' => $smsContent,
                        'notifiable_type' => get_class($maintenanceRequest),
                        'notifiable_id' => $maintenanceRequest->id,
                        'status' => 'pending',
                    ]);

                    $appUrl = config('app.url');
                    $messageParams = [
                        'from' => config('services.twilio.phone'),
                        'body' => $smsContent,
                    ];

                    if ($appUrl && !str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
                        $messageParams['statusCallback'] = url('/twilio-webhook.php'); // Direct file bypasses CSRF
                    }

                    // Log the callback URL for debugging
                    Log::info('SMS sent with callback', [
                        'to' => $vendorUser->phone,
                        'callback_url' => url('/twilio-webhook.php'),
                        'message_sid' => $notification->sid,
                    ]);

                    $message = $this->twilio->messages->create(
                        $vendorUser->phone,
                        $messageParams
                    );

                    $notification->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'provider_id' => $message->sid,
                    ]);

                    Log::info('Vendor SMS notification sent', [
                        'vendor_id' => $vendor->id,
                        'vendor_user_id' => $vendorUser->id,
                        'notification_id' => $notification->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send vendor notification', [
                    'vendor_id' => $vendor->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // ✨ Vendor doesn't have user account, use direct contact info
            try {
                if ($vendor->email) {
                    Mail::to($vendor->email)->send(new GenericNotification(
                        'New Maintenance Job Assigned',
                        $emailContent,
                        null
                    ));

                    Log::info('Vendor email sent directly (no user account)', [
                        'vendor_id' => $vendor->id,
                        'vendor_email' => $vendor->email,
                    ]);
                }

                if ($vendor->phone && $this->twilio) {
                    $this->twilio->messages->create(
                        $vendor->phone,
                        [
                            'from' => config('services.twilio.phone'),
                            'body' => $smsContent,
                        ]
                    );

                    Log::info('Vendor SMS sent directly (no user account)', [
                        'vendor_id' => $vendor->id,
                        'vendor_phone' => $vendor->phone,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send direct vendor notification', [
                    'vendor_id' => $vendor->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * ✨ NEW: Send notification to a specific user with role-specific template
     */
    protected function sendMaintenanceNotificationToUser(
        $maintenanceRequest,
        string $eventRole,
        User $user
    ): void {
        $templates = [
            // Tenant notifications (original)
            'created_tenant' => [
                'subject' => 'Maintenance Request Submitted',
                'email' => "Your maintenance request has been submitted successfully.\n\nCategory: {category}\nPriority: {priority}\nDescription: {description}\n\nWe will review your request and assign a vendor shortly.",
                'sms' => 'Your maintenance request #{id} has been submitted and will be reviewed shortly.',
            ],
            'assigned_tenant' => [
                'subject' => 'Vendor Assigned to Your Request',
                'email' => "Your maintenance request has been assigned to a vendor.\n\nVendor: {vendor}\nScheduled: {scheduled}\n\nThe vendor will contact you soon.",
                'sms' => 'Your request #{id} has been assigned to {vendor}.',
            ],
            'in_progress_tenant' => [
                'subject' => 'Work Started on Your Request',
                'email' => "The vendor has started working on your maintenance request.\n\nVendor: {vendor}",
                'sms' => 'Work has started on request #{id}.',
            ],
            'completed_tenant' => [
                'subject' => 'Maintenance Request Completed',
                'email' => "Your maintenance request has been completed.\n\nCompleted on: {completed_at}\nVendor: {vendor}\n\nThank you for your patience.",
                'sms' => 'Your request #{id} is now completed.',
            ],

            // ✨ NEW: Landlord/Manager notifications
            'created_landlord' => [
                'subject' => 'New Maintenance Request - Action Required',
                'email' => "A new maintenance request has been submitted and needs assignment.\n\nProperty: {property}\nUnit: {unit}\nTenant: {tenant}\n\nCategory: {category}\nPriority: {priority}\nDescription: {description}\n\nPlease assign a vendor as soon as possible.",
                'sms' => 'New {priority} priority maintenance request at {property} Unit {unit}. Review and assign vendor.',
            ],
            'completed_landlord' => [
                'subject' => 'Maintenance Request Completed',
                'email' => "A maintenance request has been completed.\n\nProperty: {property}\nUnit: {unit}\nTenant: {tenant}\nVendor: {vendor}\n\nCompleted on: {completed_at}",
                'sms' => 'Maintenance request #{id} at {property} Unit {unit} completed by {vendor}.',
            ],

            // ✨ NEW: Vendor notifications (kept for vendors with proper organization_id)
            'assigned_vendor' => [
                'subject' => 'New Maintenance Job Assigned',
                'email' => "You have been assigned a new maintenance job.\n\nProperty: {property}\nUnit: {unit}\nTenant: {tenant}\nTenant Phone: {tenant_phone}\n\nCategory: {category}\nPriority: {priority}\nDescription: {description}\n\nScheduled: {scheduled}\n\nPlease contact the tenant to coordinate access.",
                'sms' => 'New {priority} job assigned: {category} at {property} Unit {unit}. Contact tenant at {tenant_phone}.',
            ],
        ];

        $template = $templates[$eventRole] ?? null;
        if (!$template) {
            Log::warning('Unknown maintenance notification template', [
                'event_role' => $eventRole,
                'request_id' => $maintenanceRequest->id,
            ]);
            return;
        }

        if (!$user) {
            Log::warning('No recipient found for maintenance notification', [
                'maintenance_request_id' => $maintenanceRequest->id,
                'event_role' => $eventRole,
            ]);
            return;
        }

        // Build replacements
        $replacements = [
            '{id}' => substr($maintenanceRequest->id, 0, 8),
            '{category}' => $maintenanceRequest->category ?? 'N/A',
            '{priority}' => ucfirst($maintenanceRequest->priority ?? 'N/A'),
            '{description}' => $maintenanceRequest->description ?? 'N/A',
            '{vendor}' => $maintenanceRequest->assignedVendor?->company_name ?? 'Vendor',
            '{scheduled}' => $maintenanceRequest->scheduled_date?->format('M j, Y') ?? 'TBD',
            '{completed_at}' => $maintenanceRequest->completed_at?->format('M j, Y') ?? now()->format('M j, Y'),
            '{property}' => $maintenanceRequest->unit?->property?->name ?? 'Property',
            '{unit}' => $maintenanceRequest->unit?->unit_number ?? 'N/A',
            '{tenant}' => $maintenanceRequest->tenant?->name ?? 'Tenant',
            '{tenant_phone}' => $maintenanceRequest->tenant?->phone ?? 'N/A',
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

        // Send notification (preferences will be checked in send())
        $this->send(
            $user,
            $template['subject'],
            $emailContent,
            ['email', 'sms'],
            'maintenance',
            $maintenanceRequest
        );
    }

    /**
     * ✨ NEW: Notify all landlords and managers in the organization
     */
    protected function notifyLandlordsAndManagers($maintenanceRequest, string $eventRole): void
    {
        // Get organization from the maintenance request
        $organizationId = $maintenanceRequest->unit?->property?->organization_id;

        if (!$organizationId) {
            Log::warning('Cannot notify landlords/managers - no organization found', [
                'maintenance_request_id' => $maintenanceRequest->id,
            ]);
            return;
        }

        // Get all landlords and managers in the organization
        $landlordManagers = User::where('organization_id', $organizationId)
            ->whereIn('role', ['admin', 'manager', 'landlord'])
            ->get();

        foreach ($landlordManagers as $user) {
            $this->sendMaintenanceNotificationToUser(
                $maintenanceRequest,
                $eventRole,
                $user
            );
        }

        Log::info('Notified landlords/managers', [
            'maintenance_request_id' => $maintenanceRequest->id,
            'event_role' => $eventRole,
            'recipient_count' => $landlordManagers->count(),
        ]);
    }
}
