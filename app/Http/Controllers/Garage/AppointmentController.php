<?php

namespace App\Http\Controllers\Garage;

use App\Http\Controllers\Controller;
use App\Models\InspectionAppointment;
use App\Notifications\ClientNotice;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $me  = $request->user()->id;
        $tab = in_array($request->query('tab'), ['mine', 'done'], true) ? $request->query('tab') : 'new';

        $query = InspectionAppointment::with('vehicle', 'user');

        $appointments = match ($tab) {
            'mine'  => $query->where('garage_id', $me)->where('status', 'confirmed')->orderBy('scheduled_date')->get(),
            'done'  => $query->where('garage_id', $me)->where('status', 'completed')->latest('updated_at')->get(),
            default => $query->where('status', 'pending')->whereNull('garage_id')->orderBy('scheduled_date')->get(),
        };

        return view('garage.appointments.index', compact('appointments', 'tab'));
    }

    public function confirm(Request $request, InspectionAppointment $appointment)
    {
        // Mise à jour atomique : un seul garage peut prendre la demande.
        $updated = InspectionAppointment::where('id', $appointment->id)
            ->where('status', 'pending')
            ->whereNull('garage_id')
            ->update(['status' => 'confirmed', 'garage_id' => $request->user()->id]);

        if (! $updated) {
            return back()->withErrors(['appointment' => "Cette demande n'est plus disponible."]);
        }

        $appointment->load('vehicle', 'user');
        $appointment->user->notify(new ClientNotice(
            "Votre rendez-vous du {$appointment->scheduled_date->locale('fr')->translatedFormat('d F Y')} pour votre {$appointment->vehicle->brand} {$appointment->vehicle->model} est confirmé par {$request->user()->name}.",
            route('client.inspections.index', [], false),
        ));

        return redirect()->route('garage.appointments.index', ['tab' => 'mine'])->with('success', 'Rendez-vous confirmé.');
    }

    public function complete(Request $request, InspectionAppointment $appointment)
    {
        $this->authorizeOwner($request, $appointment);

        if ($appointment->status !== 'confirmed') {
            return back()->withErrors(['appointment' => 'Ce rendez-vous ne peut pas être clôturé.']);
        }
        if ($appointment->scheduled_date->isFuture()) {
            return back()->withErrors(['appointment' => "Le rendez-vous n'a pas encore eu lieu."]);
        }

        $data = $request->validate(
            ['new_expiry' => ['required', 'date', 'after:today']],
            ['new_expiry.required' => "Indiquez la nouvelle date d'expiration.", 'new_expiry.after' => "La date d'expiration doit être dans le futur."]
        );

        $appointment->update(['status' => 'completed']);
        $appointment->vehicle->update(['technical_visit_expires_at' => $data['new_expiry']]);

        $appointment->load('user');
        $appointment->user->notify(new ClientNotice(
            "Votre visite technique pour {$appointment->vehicle->brand} {$appointment->vehicle->model} est effectuée. Nouvelle échéance : " . \Carbon\Carbon::parse($data['new_expiry'])->format('d/m/Y') . '.',
            route('client.vehicles.index', [], false),
        ));

        return redirect()->route('garage.appointments.index', ['tab' => 'done'])->with('success', 'Visite marquée comme effectuée.');
    }

    /** Le garage ne peut finalement pas honorer le rendez-vous : il repart dans la liste des demandes. */
    public function release(Request $request, InspectionAppointment $appointment)
    {
        $this->authorizeOwner($request, $appointment);

        if ($appointment->status === 'confirmed') {
            $appointment->update(['status' => 'pending', 'garage_id' => null]);

            $appointment->load('vehicle', 'user');
            $appointment->user->notify(new ClientNotice(
                "Le garage ne peut pas honorer votre rendez-vous du {$appointment->scheduled_date->locale('fr')->translatedFormat('d F Y')}. Votre demande est de nouveau en attente de confirmation.",
                route('client.inspections.index', [], false),
            ));
        }

        return redirect()->route('garage.appointments.index')->with('success', 'Rendez-vous libéré.');
    }

    private function authorizeOwner(Request $request, InspectionAppointment $appointment): void
    {
        abort_unless($appointment->garage_id === $request->user()->id, 403);
    }
}