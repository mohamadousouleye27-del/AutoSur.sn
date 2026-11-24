<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class WaveGateway implements PaymentGateway
{
    private function http()
    {
        return Http::withToken(config('payments.wave.api_key'))
            ->acceptJson()
            ->timeout(15);
    }

    public function createCheckout(Payment $payment): array
    {
        $response = $this->http()->post(config('payments.wave.base_url') . '/v1/checkout/sessions', [
            'amount'           => (string) $payment->amount,
            'currency'         => 'XOF',
            'client_reference' => $payment->reference,
            'success_url'      => route('client.payments.callback', $payment),
            'error_url'        => route('client.payments.callback', $payment),
        ]);

        $response->throw();

        return [
            'session_id' => $response['id'],
            'url'        => $response['wave_launch_url'],
        ];
    }

    public function fetchStatus(Payment $payment): string
    {
        if (! $payment->provider_session_id) {
            return 'pending';
        }

        $response = $this->http()->get(
            config('payments.wave.base_url') . '/v1/checkout/sessions/' . $payment->provider_session_id
        );

        $response->throw();

        return match (true) {
            $response['payment_status'] === 'succeeded' => 'paid',
            $response['payment_status'] === 'cancelled' => 'failed',
            $response['checkout_status'] === 'expired'  => 'expired',
            default                                     => 'pending',
        };
    }
}