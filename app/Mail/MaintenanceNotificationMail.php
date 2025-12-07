<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $statusMessage;
    public string $statusColor;

    public function __construct(
        public MaintenanceRequest $maintenanceRequest,
        public string $event,
        public User $recipient
    ) {
        $this->setEventDetails();
    }

    protected function setEventDetails(): void
    {
        $events = [
            'created' => [
                'subject' => 'Maintenance Request Submitted',
                'message' => 'Your maintenance request has been submitted and is being reviewed.',
                'color' => '#3b82f6', // blue
            ],
            'assigned' => [
                'subject' => 'Vendor Assigned to Your Request',
                'message' => 'A vendor has been assigned to handle your maintenance request.',
                'color' => '#8b5cf6', // purple
            ],
            'in_progress' => [
                'subject' => 'Work Started on Your Request',
                'message' => 'The vendor has started working on your maintenance request.',
                'color' => '#f59e0b', // amber
            ],
            'completed' => [
                'subject' => 'Maintenance Request Completed',
                'message' => 'Your maintenance request has been completed.',
                'color' => '#10b981', // green
            ],
            'cancelled' => [
                'subject' => 'Maintenance Request Cancelled',
                'message' => 'Your maintenance request has been cancelled.',
                'color' => '#6b7280', // gray
            ],
        ];

        $details = $events[$this->event] ?? $events['created'];
        
        $this->emailSubject = $details['subject'];
        $this->statusMessage = $details['message'];
        $this->statusColor = $details['color'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject . ' - #' . substr($this->maintenanceRequest->id, 0, 8),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user.maintenance-notification',
            with: [
                'maintenanceRequest' => $this->maintenanceRequest,
                'event' => $this->event,
                'recipient' => $this->recipient,
                'emailSubject' => $this->emailSubject,
                'statusMessage' => $this->statusMessage,
                'statusColor' => $this->statusColor,
                'previewText' => $this->statusMessage,
            ],
        );
    }
}
