<?php

namespace App\Http\Controllers\Insurer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $offers = $request->user()->insuranceOffers()
            ->where('status', 'accepted')
            ->with('quote.vehicle', 'quote.user')
            ->latest()
            ->get();

        return view('insurer.subscriptions.index', [
            'offers'          => $offers,
            'totalPremium'    => $offers->sum('annual_premium'),
            'totalCommission' => $offers->sum(fn ($o) => $o->commissionAmount()),
        ]);
    }
}