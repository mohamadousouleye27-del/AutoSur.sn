<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\InspectionAppointment;
use App\Models\InsuranceOffer;
use App\Models\InsuranceQuote;
use App\Models\SosRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'roles' => User::select('role', DB::raw('count(*) as n'))->groupBy('role')->pluck('n', 'role'),
            'pendingPartners' => User::whereIn('role', User::PARTNER_ROLES)->where('partner_status', 'pending')->count(),
            'vehicles'      => Vehicle::count(),
            'documents'     => Document::count(),
            'openQuotes'    => InsuranceQuote::where('status', 'open')->count(),
            'pendingVisits' => InspectionAppointment::where('status', 'pending')->count(),
            'activeSos'     => SosRequest::whereIn('status', ['searching', 'accepted'])->count(),
            'commissions'   => InsuranceOffer::where('status', 'accepted')->get()->sum(fn ($o) => $o->commissionAmount()),
            'fees'          => InspectionAppointment::where('status', 'completed')->sum('fee'),
            'recent'        => User::latest()->take(6)->get(),
        ]);
    }
}