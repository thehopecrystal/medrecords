<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $name = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your MedRecords verification code');
    }

    public function content(): Content
    {
        // Deliberately not the auth.otp web view — emails render without a
        // session/auth context, so they need a standalone template.
        return new Content(view: 'emails.email-verification-otp-mail');
    }
}
