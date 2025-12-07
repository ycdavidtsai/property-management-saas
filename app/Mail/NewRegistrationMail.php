<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $organizationName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Registration: ' . $this->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-registration',
            with: [
                'user' => $this->user,
                'organizationName' => $this->organizationName,
                'previewText' => 'A new user has registered: ' . $this->user->name,
            ],
        );
    }
}
