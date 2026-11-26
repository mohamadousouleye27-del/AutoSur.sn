<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(private string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $from  = $email->getFrom()[0];

        $payload = [
            'sender'  => [
                'name'  => $from->getName() ?: config('mail.from.name'),
                'email' => $from->getAddress(),
            ],
            'to'      => array_map(
                fn ($address) => array_filter(['email' => $address->getAddress(), 'name' => $address->getName()]),
                $email->getTo()
            ),
            'subject' => $email->getSubject(),
        ];

        if ($email->getReplyTo()) {
            $payload['replyTo'] = ['email' => $email->getReplyTo()[0]->getAddress()];
        }
        if ($html = $email->getHtmlBody()) {
            $payload['htmlContent'] = $html;
        }
        if ($text = $email->getTextBody()) {
            $payload['textContent'] = $text;
        }

        $response = Http::withHeaders(['api-key' => $this->apiKey, 'accept' => 'application/json'])
            ->timeout(15)
            ->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Brevo : ' . $response->status() . ' ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}