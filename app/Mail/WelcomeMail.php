<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WelcomeMail extends Mailable
{
    public function __construct(public User $user, public string $verifyUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Bienvenue sur AutoSur, {$this->user->firstName()} !");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome', text: 'emails.welcome-text');
    }
}