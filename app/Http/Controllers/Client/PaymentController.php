<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InspectionAppointment;
use App\Models\Payment;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentGateway $gateway,
        private PaymentService $payments,
    ) {}

    /** Démarre le paiement des frais d'un rendez-vous de visite technique. */
    public function payInspection(Request $request, InspectionAppointment $inspection)
    {
        abort_unless($inspection->user_id === $request->user()->id, 403);

        if (! $inspection->canCancel()) {
            return back()->withErrors(['payment' => "Ce rendez-vous n'est plus payable."]);
        }
        if ($inspection->isPaid()) {
            return back()->withErrors(['payment' => 'Ce rendez-vous est déjà payé.']);
        }

        // Réutilise un paiement récent resté en attente plutôt que d'en créer un nouveau.
        $payment = $inspection->payments()
            ->where('status', 'pending')
            ->whereNotNull('checkout_url')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->latest()->first();

        if (! $payment) {
            // Le montant vient toujours de la base de données, jamais du navigateur.
            $payment = $inspection->payments()->create([
                'user_id'   => $request->user()->id,
                'amount'    => $inspection->fee,
                'provider'  => config('payments.driver'),
                'reference' => (string) Str::uuid(),
            ]);

            try {
                $checkout = $this->gateway->createCheckout($payment);
            } catch (\Throwable $e) {
                report($e);
                $payment->update(['status' => 'failed']);

                return back()->withErrors(['payment' => 'Le service de paiement est momentanément indisponible. Réessayez dans un instant.']);
            }

            $payment->update([
                'provider_session_id' => $checkout['session_id'],
                'checkout_url'        => $checkout['url'],
            ]);
        }

        return redirect()->away($payment->checkout_url);
    }

    /** Retour du client après la page de paiement : on vérifie auprès du prestataire. */
    public function callback(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        try {
            $this->payments->sync($payment);
        } catch (\Throwable $e) {
            report($e);
        }

        $redirect = redirect()->route('client.inspections.index');

        return match ($payment->status) {
            'paid'            => $redirect->with('success', 'Paiement reçu. Merci !'),
            'failed', 'expired' => $redirect->withErrors(['payment' => "Le paiement n'a pas abouti. Vous pouvez réessayer."]),
            default           => $redirect->with('success', 'Paiement en cours de vérification : le statut sera mis à jour dans quelques instants.'),
        };
    }

    /** Notification du prestataire (sans connexion, sans CSRF) : elle ne fait que déclencher une vérification. */
    public function webhook(Request $request)
    {
        $payload   = $request->all();
        $sessionId = data_get($payload, 'data.id');
        $reference = data_get($payload, 'data.client_reference');

        $payment = null;
        if ($sessionId) {
            $payment = Payment::where('provider_session_id', $sessionId)->first();
        }
        if (! $payment && $reference) {
            $payment = Payment::where('reference', $reference)->first();
        }

        if ($payment) {
            try {
                $this->payments->sync($payment); // l'état est redemandé à Wave : le contenu reçu n'est jamais cru
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json(['ok' => true]);
    }

    /* ---------- Simulation (développement uniquement) ---------- */

    public function simulation(Payment $payment)
    {
        $this->guardSimulation($payment);

        return view('client.payments.simulation', compact('payment'));
    }

    public function simulate(Request $request, Payment $payment)
    {
        $this->guardSimulation($payment);

        $outcome = $request->validate(['outcome' => ['required', 'in:paid,failed']])['outcome'];
        $this->payments->apply($payment, $outcome);

        return redirect()->route('client.payments.callback', $payment);
    }

    private function guardSimulation(Payment $payment): void
    {
        abort_unless(
            app()->environment('local')
                && config('payments.driver') === 'simulation'
                && $payment->provider === 'simulation'
                && $payment->user_id === auth()->id(),
            404
        );
    }
}