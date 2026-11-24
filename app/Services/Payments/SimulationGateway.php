<?php

namespace App\Services\Payments;

use App\Models\Payment;

class SimulationGateway implements PaymentGateway
{
    public function createCheckout(Payment $payment): array
    {
        return [
            'session_id' => 'sim_' . $payment->reference,
            'url'        => route('client.payments.simulation', $payment, false),
        ];
    }

    public function fetchStatus(Payment $payment): string
    {
        return $payment->status;
    }
}