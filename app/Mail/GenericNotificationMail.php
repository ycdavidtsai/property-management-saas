<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $messageContent,
        public ?Notification $notification = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user.generic-notification',
            with: [
                'emailSubject' => $this->emailSubject,
                'messageContent' => $this->messageContent,
                'notification' => $this->notification,
                'previewText' => \Illuminate\Support\Str::limit(strip_tags($this->messageContent), 100),
            ],
        );
    }
}
