<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsGateway
{
    public function send(string $phone, string $text): bool
    {
        return match (config('alerts.sms_driver')) {
            'orange' => $this->sendViaOrange($phone, $text),
            default  => $this->sendToLog($phone, $text),
        };
    }

    /** Développement : le SMS est écrit dans storage/logs/laravel.log. */
    private function sendToLog(string $phone, string $text): bool
    {
        Log::info("SMS vers {$phone} : {$text}");

        return true;
    }

    private function sendViaOrange(string $phone, string $text): bool
    {
        try {
            $sender = (string) config('sms.orange.sender_number');

            $body = ['outboundSMSMessageRequest' => [
                'address'                => 'tel:' . $phone,
                'senderAddress'          => 'tel:+' . $sender,
                'outboundSMSTextMessage' => ['message' => $text],
            ]];

            if ($name = config('sms.orange.sender_name')) {
                $body['outboundSMSMessageRequest']['senderName'] = $name;
            }

            $url = 'https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B' . $sender . '/requests';

            $response = Http::withToken($this->orangeToken())->acceptJson()->timeout(15)->post($url, $body);

            // Jeton expiré : on en redemande un et on retente une seule fois.
            if ($response->status() === 401) {
                $response = Http::withToken($this->orangeToken(true))->acceptJson()->timeout(15)->post($url, $body);
            }

            if ($response->successful()) {
                return true;
            }

            // Le texte du SMS (qui contient un code) n'est volontairement pas journalisé.
            Log::error('Échec envoi SMS Orange', ['status' => $response->status(), 'body' => $response->json()]);
        } catch (\Throwable $e) {
            report($e);
        }

        return false;
    }

    /** Le jeton Orange est valable 1 heure : on le garde 55 minutes en cache. */
    private function orangeToken(bool $refresh = false): string
    {
        if ($refresh) {
            Cache::forget('orange_sms_token');
        }

        return Cache::remember('orange_sms_token', now()->addMinutes(55), function () {
            $response = Http::withHeaders(['Authorization' => config('sms.orange.authorization')])
                ->asForm()->acceptJson()->timeout(15)
                ->post('https://api.orange.com/oauth/v3/token', ['grant_type' => 'client_credentials']);

            $response->throw();

            return $response['access_token'];
        });
    }
}