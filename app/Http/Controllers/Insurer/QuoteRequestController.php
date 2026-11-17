<?php

namespace App\Http\Controllers\Insurer;

use App\Http\Controllers\Controller;
use App\Models\InsuranceOffer;
use App\Models\InsuranceQuote;
use App\Models\User;
use App\Notifications\NewInsuranceOffer;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function index(Request $request)
    {
        $quotes = InsuranceQuote::where('status', 'open')
            ->with('vehicle')
            ->withCount(['offers as my_offers_count' => fn ($q) => $q->where('insurer_id', $request->user()->id)])
            ->latest()
            ->get();

        return view('insurer.requests.index', compact('quotes'));
    }

    public function show(Request $request, InsuranceQuote $quote)
    {
        $offer = $quote->offers()->where('insurer_id', $request->user()->id)->first();

        abort_unless($quote->status === 'open' || $offer, 403);

        return view('insurer.requests.show', [
            'quote' => $quote->load('vehicle', 'user'),
            'offer' => $offer,
            'rate'  => $this->rate($request->user()),
        ]);
    }

    /** Crée ou modifie l'offre de l'assureur connecté pour cette demande. */
    public function store(Request $request, InsuranceQuote $quote)
    {
        abort_unless($quote->status === 'open', 403);

        $data = $request->validate([
            'annual_premium' => ['required', 'integer', 'between:10000,5000000'],
            'valid_until'    => ['required', 'date', 'after:today'],
            'details'        => ['nullable', 'string', 'max:600'],
        ], [
            'annual_premium.required' => 'Indiquez la prime annuelle.',
            'annual_premium.between'  => 'La prime doit être comprise entre 10 000 et 5 000 000 FCFA.',
            'valid_until.after'       => 'La date de validité doit être dans le futur.',
        ]);

        $offer = InsuranceOffer::firstOrNew([
            'insurance_quote_id' => $quote->id,
            'insurer_id'         => $request->user()->id,
        ]);

        if ($offer->exists && $offer->status !== 'pending') {
            return back()->withErrors(['annual_premium' => 'Cette offre ne peut plus être modifiée.']);
        }

        if (! $offer->exists) {
            $offer->commission_rate = $this->rate($request->user());
        }

        $offer->fill($data)->save();

        if ($offer->wasRecentlyCreated) {
            $quote->load('vehicle', 'user');
            $quote->user->notify(new NewInsuranceOffer(
                "Nouvelle offre de {$request->user()->name} pour votre {$quote->vehicle->brand} {$quote->vehicle->model} : {$offer->formattedPremium()} par an.",
                route('client.insurance.show', $quote, false),
            ));
        }

        return redirect()->route('assurance.offers.index')->with('success', 'Votre offre a été envoyée au client.');
    }

    /** Taux de commission de l'assureur, borné entre 5 % et 12 %. */
    private function rate(User $user): float
    {
        return max(5, min(12, (float) $user->commission_rate));
    }
}