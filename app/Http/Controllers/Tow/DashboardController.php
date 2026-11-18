<?php

namespace App\Http\Controllers\Tow;

use App\Http\Controllers\Controller;
use App\Models\SosRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        return view('tow.dashboard', [
            'available' => $me->is_available,
            'openCount' => $me->is_available ? SosRequest::openFor($me->id)->count() : 0,
            'active'    => SosRequest::with('vehicle')->where('driver_id', $me->id)->where('status', 'accepted')->first(),
            'doneCount' => SosRequest::where('driver_id', $me->id)->where('status', 'completed')->count(),
        ]);
    }

    public function toggle(Request $request)
    {
        $me = $request->user();
        $me->forceFill(['is_available' => ! $me->is_available])->save();

        return back();
    }
}