<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DexNotifySmsService
{
    public function send(
        string $phone,
        string $message,
        ?string $idempotencyKey = null
    ): array {
        $apiKey = config('services.dexnotify.api_key');
        $baseUrl = config('services.dexnotify.base_url');
        $sender = config('services.dexnotify.sender');

        if (!$apiKey || !$baseUrl) {
            throw new RuntimeException(
                'Configuration DexNotify manquante.'
            );
        }

 $response = Http::timeout(20)
    ->withHeaders([
        'x-api-key' => $apiKey,
        'Idempotency-Key' => $idempotencyKey ?? (string) Str::uuid(),
    ])
    ->post(
        rtrim($baseUrl, '/') . '/api/v1/sms/messages',
        [
            'sender' => $sender,
            'to' => $phone,
            'content' => $message,
        ]
    );

$response->throw();

return $response->json() ?? [];
    }
}