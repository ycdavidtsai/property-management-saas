<?php

namespace App\Mail;

use App\Models\BroadcastMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $messageContent,
        public User $recipient,
        public ?User $sender = null,
        public ?string $organizationName = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user.broadcast',
            with: [
                'title' => $this->title,
                'messageContent' => $this->messageContent,
                'recipient' => $this->recipient,
                'sender' => $this->sender,
                'organizationName' => $this->organizationName ?? config('app.name'),
                'previewText' => \Illuminate\Support\Str::limit(strip_tags($this->messageContent), 100),
            ],
        );
    }
}
