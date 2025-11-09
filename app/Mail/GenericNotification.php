<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,  // Changed from $subject to $emailSubject
        public string $emailContent,   // Changed from $content to $emailContent
        public Notification $notification
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,  // Use our custom property
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.generic-notification',
            with: [
                'emailContent' => $this->emailContent,
                'notification' => $this->notification,
            ]
        );
    }
}
