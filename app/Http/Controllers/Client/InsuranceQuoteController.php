<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InsuranceOffer;
use App\Models\InsuranceQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InsuranceQuoteController extends Controller
{
    public function index(Request $request)
    {
        return view('client.insurance.index', [
            'quotes' => $request->user()->insuranceQuotes()
                ->with('vehicle')->withCount('offers')->latest()->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('client.insurance.create', [
            'vehicles' => $request->user()->vehicles()->orderBy('brand')->get(),
            'selected' => $request->query('vehicle'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('user_id', $request->user()->id)],
            'coverage'   => ['required', Rule::in(array_keys(InsuranceQuote::COVERAGES))],
            'usage'      => ['required', Rule::in(array_keys(InsuranceQuote::USAGES))],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ], [
            'vehicle_id.required'       => 'Choisissez un véhicule.',
            'vehicle_id.exists'         => 'Véhicule invalide.',
            'start_date.after_or_equal' => 'La date ne peut pas être dans le passé.',
        ]);

        $quote = $request->user()->insuranceQuotes()->create($data);

        return redirect()->route('client.insurance.show', $quote)
            ->with('success', 'Demande envoyée. Les assureurs partenaires vont vous répondre.');
    }

    public function show(InsuranceQuote $quote)
    {
        $this->authorizeOwner($quote);

        return view('client.insurance.show', [
            'quote'  => $quote->load('vehicle'),
            'offers' => $quote->offers()->with('insurer')->orderBy('annual_premium')->get(),
        ]);
    }

    public function accept(InsuranceOffer $offer)
    {
        $quote = $offer->quote;
        $this->authorizeOwner($quote);

        if ($quote->status !== 'open' || $offer->status !== 'pending') {
            return back()->withErrors(['offer' => "Cette offre n'est plus disponible."]);
        }
        if ($offer->isExpired()) {
            return back()->withErrors(['offer' => 'Cette offre a expiré.']);
        }

        DB::transaction(function () use ($quote, $offer) {
            $quote->offers()->where('id', '!=', $offer->id)->update(['status' => 'rejected']);
            $offer->update(['status' => 'accepted']);
            $quote->update(['status' => 'accepted', 'accepted_offer_id' => $offer->id]);
        });

        return redirect()->route('client.insurance.show', $quote)
            ->with('success', 'Offre acceptée. Le paiement et la souscription seront disponibles prochainement.');
    }

    /** Annulation d'une demande encore ouverte. */
    public function destroy(InsuranceQuote $quote)
    {
        $this->authorizeOwner($quote);

        if ($quote->status === 'open') {
            DB::transaction(function () use ($quote) {
                $quote->offers()->where('status', 'pending')->update(['status' => 'rejected']);
                $quote->update(['status' => 'cancelled']);
            });
        }

        return redirect()->route('client.insurance.index')->with('success', 'Demande annulée.');
    }

    private function authorizeOwner(InsuranceQuote $quote): void
    {
        abort_unless($quote->user_id === auth()->id(), 403);
    }
}