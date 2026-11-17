<?php

namespace App\Http\Controllers\Insurer;

use App\Http\Controllers\Controller;
use App\Models\InsuranceQuote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $offers = $user->insuranceOffers()->get();
        $accepted = $offers->where('status', 'accepted');

        return view('insurer.dashboard', [
            'newRequests' => InsuranceQuote::where('status', 'open')
                ->whereDoesntHave('offers', fn ($q) => $q->where('insurer_id', $user->id))
                ->count(),
            'pending'    => $offers->where('status', 'pending')->count(),
            'accepted'   => $accepted->count(),
            'commission' => $accepted->sum(fn ($o) => $o->commissionAmount()),
        ]);
    }
}