<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Notifications\ClientNotice;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private PaymentGateway $gateway) {}

    /** Demande l'état réel au prestataire et met à jour le paiement. */
    public function sync(Payment $payment): Payment
    {
        if ($payment->status !== 'paid') {
            $this->apply($payment, $this->gateway->fetchStatus($payment));
        }

        return $payment->refresh();
    }

    /** Applique un statut (idempotent : un paiement « payé » ne change plus jamais). */
    public function apply(Payment $payment, string $status): void
    {
        if ($status === 'pending') {
            return;
        }

        DB::transaction(function () use ($payment, $status) {
            $locked = Payment::lockForUpdate()->find($payment->id);

            if (! $locked || $locked->status === 'paid' || $locked->status === $status) {
                return;
            }

            $locked->forceFill([
                'status'  => $status,
                'paid_at' => $status === 'paid' ? now() : null,
            ])->save();

            if ($status === 'paid') {
                $locked->user->notify(new ClientNotice(
                    "Paiement de {$locked->formattedAmount()} reçu. Merci !",
                    route('client.inspections.index', [], false),
                ));
            }
        });
    }
}