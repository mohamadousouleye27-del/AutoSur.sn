<?php

namespace App\Notifications\Channels;

use App\Services\SmsGateway;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        app(SmsGateway::class)->send($notifiable->phone, $notification->toSms($notifiable));
    }
}