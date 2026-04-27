<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password',
        );
    }

    public function content(): Content
    {
        $frontendBaseUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $resetUrl = $frontendBaseUrl . '/reset-password?token=' . urlencode($this->token) . '&email=' . urlencode($this->user->email);

        return new Content(
            view: 'emails.auth.reset-password',
            with: [
                'user' => $this->user,
                'resetUrl' => $resetUrl,
                'expiresIn' => 60, // minutes
            ],
        );
    }
}
