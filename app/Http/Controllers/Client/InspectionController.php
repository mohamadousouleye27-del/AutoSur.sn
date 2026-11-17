<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InspectionAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        return view('client.inspections.index', [
            'appointments' => $request->user()->inspectionAppointments()
                ->with('vehicle')->orderByDesc('scheduled_date')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('client.inspections.create', [
            'vehicles' => $request->user()->vehicles()->orderBy('brand')->get(),
            'selected' => $request->query('vehicle'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'     => ['required', Rule::exists('vehicles', 'id')->where('user_id', $request->user()->id)],
            'category'       => ['required', Rule::in(array_keys(InspectionAppointment::CATEGORIES))],
            'scheduled_date' => [
                'required', 'date', 'after:today',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isSunday()) {
                        $fail('Les rendez-vous ne sont pas disponibles le dimanche.');
                    }
                },
            ],
            'time_slot'      => ['required', Rule::in(array_keys(InspectionAppointment::TIME_SLOTS))],
            'city'           => ['required', 'string', 'max:80'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ], [
            'vehicle_id.required'   => 'Choisissez un véhicule.',
            'scheduled_date.after'  => 'Choisissez une date à partir de demain.',
            'city.required'         => 'Indiquez votre ville ou quartier.',
        ]);

        $already = InspectionAppointment::where('vehicle_id', $data['vehicle_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($already) {
            return back()->withInput()->withErrors([
                'vehicle_id' => 'Un rendez-vous est déjà en cours pour ce véhicule.',
            ]);
        }

        $data['fee'] = InspectionAppointment::CATEGORIES[$data['category']]['fee'];

        $request->user()->inspectionAppointments()->create($data);

        return redirect()->route('client.inspections.index')
            ->with('success', 'Demande de rendez-vous envoyée. Un garage partenaire va la confirmer.');
    }

    /** Annulation (le rendez-vous est conservé dans l'historique). */
    public function destroy(InspectionAppointment $inspection)
    {
        abort_unless($inspection->user_id === auth()->id(), 403);

        if ($inspection->canCancel()) {
            $inspection->update(['status' => 'cancelled']);
        }

        return redirect()->route('client.inspections.index')->with('success', 'Rendez-vous annulé.');
    }
}