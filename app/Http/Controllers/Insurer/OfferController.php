<?php

namespace App\Http\Controllers\Insurer;

use App\Http\Controllers\Controller;
use App\Models\InsuranceOffer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        return view('insurer.offers.index', [
            'offers' => $request->user()->insuranceOffers()->with('quote.vehicle')->latest()->get(),
        ]);
    }

    public function destroy(Request $request, InsuranceOffer $offer)
    {
        abort_unless($offer->insurer_id === $request->user()->id, 403);

        if ($offer->status !== 'pending') {
            return back()->withErrors(['offer' => 'Seule une offre en attente peut être retirée.']);
        }

        $offer->delete();

        return back()->with('success', 'Offre retirée.');
    }
}