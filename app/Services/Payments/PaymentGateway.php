<?php

namespace App\Services\Payments;

use App\Models\Payment;

interface PaymentGateway
{
    /** Crée la session chez le prestataire : ['session_id' => ..., 'url' => ...]. */
    public function createCheckout(Payment $payment): array;

    /** Interroge le prestataire : 'paid', 'failed', 'expired' ou 'pending'. */
    public function fetchStatus(Payment $payment): string;
}