<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionAppointment;
use App\Models\InsuranceOffer;
use Illuminate\Http\Request;
use App\Models\Payment;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month')) ? $request->query('month') : null;

        $inMonth = function ($query) use ($month) {
            if ($month) {
                [$y, $m] = explode('-', $month);
                $query->whereYear('updated_at', $y)->whereMonth('updated_at', $m);
            }
        };

        $offers = InsuranceOffer::with('quote.vehicle', 'insurer')
            ->where('status', 'accepted')->tap($inMonth)->latest('updated_at')->get();

        $visits = InspectionAppointment::with('vehicle', 'garage')
            ->where('status', 'completed')->tap($inMonth)->latest('updated_at')->get();

        $payments = Payment::with('user')
            ->where('status', 'paid')->tap($inMonth)->latest('updated_at')->get();    

        return view('admin.finances.index', [
            'month'           => $month,
            'offers'          => $offers,
            'visits'          => $visits,
            'totalCommission' => $offers->sum(fn ($o) => $o->commissionAmount()),
            'totalFees'       => $visits->sum('fee'),
            'payments'  => $payments,
            'totalPaid' => $payments->sum('amount'),
        ]);
    }
}