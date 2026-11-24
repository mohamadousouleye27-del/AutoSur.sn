<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpiryReminder extends Notification
{
    public function __construct(
        public string $message,
        public string $url,
        public string $stage,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (Setting::bool('alerts_mail', (bool) config('alerts.channels.mail')) && $notifiable->email && $notifiable->email_verified_at) {
            $channels[] = 'mail';
        }
        if (config('alerts.channels.sms') && $notifiable->phone) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url'     => $this->url,
            'stage'   => $this->stage,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("AutoSur : rappel d'échéance")
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('Ouvrir AutoSur', url($this->url));
    }

    public function toSms(object $notifiable): string
    {
        return 'AutoSur : ' . $this->message;
    }
}