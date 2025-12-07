<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $messageContent
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Inquiry: ' . $this->name,
            replyTo: [
                new Address($this->email, $this->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.contact-inquiry',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'messageContent' => $this->messageContent,
                'previewText' => 'New inquiry from ' . $this->name,
            ],
        );
    }
}
