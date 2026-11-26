<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OtpMail extends Mailable
{
    public function __construct(
        public User $user,
        public string $code,
        public int $minutes,
        public string $purpose,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: match ($this->purpose) {
            'reset' => 'Réinitialisation de votre mot de passe AutoSur',
            'login' => 'Votre code de connexion AutoSur',
            default => 'Votre code de vérification AutoSur',
        });
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp', text: 'emails.otp-text');
    }
}