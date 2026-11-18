<?php

namespace App\Http\Controllers\Garage;

use App\Http\Controllers\Controller;
use App\Models\InspectionAppointment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $me = $request->user()->id;

        return view('garage.dashboard', [
            'newCount'  => InspectionAppointment::where('status', 'pending')->whereNull('garage_id')->count(),
            'confirmed' => InspectionAppointment::with('vehicle', 'user')
                ->where('garage_id', $me)->where('status', 'confirmed')
                ->orderBy('scheduled_date')->get(),
            'doneCount' => InspectionAppointment::where('garage_id', $me)->where('status', 'completed')->count(),
        ]);
    }
}