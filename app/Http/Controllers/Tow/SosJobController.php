<?php

namespace App\Http\Controllers\Tow;

use App\Http\Controllers\Controller;
use App\Models\SosRequest;
use App\Notifications\ClientNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SosJobController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        $active = SosRequest::with('vehicle', 'user')
            ->where('driver_id', $me->id)->where('status', 'accepted')->first();

        $requests = ($me->is_available && ! $active)
            ? SosRequest::with('vehicle')->openFor($me->id)->latest()->get()
            : collect();

        return view('tow.requests.index', [
            'active'    => $active,
            'requests'  => $requests,
            'available' => $me->is_available,
        ]);
    }

    /** Nombre de demandes ouvertes (actualisation automatique de la liste). */
    public function count(Request $request)
    {
        $me = $request->user();

        return response()->json([
            'count' => $me->is_available ? SosRequest::openFor($me->id)->count() : 0,
        ]);
    }

    public function show(Request $request, SosRequest $sos)
    {
        $mine = $sos->driver_id === $request->user()->id;

        abort_unless($mine || ($sos->status === 'searching' && $sos->driver_id === null), 403);

        return view('tow.requests.show', [
            'sos'  => $sos->load('vehicle', 'user'),
            'mine' => $mine,
        ]);
    }

    public function accept(Request $request, SosRequest $sos)
    {
        $me = $request->user();

        if (! $me->is_available) {
            return back()->withErrors(['sos' => 'Passez en « disponible » pour accepter une demande.']);
        }
        if (SosRequest::where('driver_id', $me->id)->where('status', 'accepted')->exists()) {
            return back()->withErrors(['sos' => 'Terminez votre intervention en cours avant d\'en accepter une autre.']);
        }

        // Mise à jour atomique : un seul dépanneur obtient la demande.
        $updated = SosRequest::where('id', $sos->id)
            ->where('status', 'searching')
            ->whereNull('driver_id')
            ->update(['driver_id' => $me->id, 'status' => 'accepted', 'updated_at' => now()]);

        if (! $updated) {
            return redirect()->route('depanneur.requests.index')
                ->withErrors(['sos' => "Cette demande n'est plus disponible."]);
        }

        $sos->load('user');
        $sos->user->notify(new ClientNotice(
            "Un dépanneur est en route : {$me->name} ({$me->phone}).",
            route('client.sos.show', $sos, false),
        ));

        return redirect()->route('depanneur.requests.show', $sos)->with('success', 'Demande acceptée. Le client a été prévenu.');
    }

    public function decline(Request $request, SosRequest $sos)
    {
        DB::table('sos_request_declines')->insertOrIgnore([
            'sos_request_id' => $sos->id,
            'driver_id'      => $request->user()->id,
        ]);

        return redirect()->route('depanneur.requests.index')->with('success', 'Demande ignorée.');
    }

    public function complete(Request $request, SosRequest $sos)
    {
        abort_unless($sos->driver_id === $request->user()->id, 403);

        if ($sos->status === 'accepted') {
            $sos->update(['status' => 'completed']);
            $sos->load('user');
            $sos->user->notify(new ClientNotice(
                'Votre intervention de dépannage est terminée.',
                route('client.sos.show', $sos, false),
            ));
        }

        return redirect()->route('depanneur.jobs.index')->with('success', 'Intervention terminée.');
    }

    /** Le dépanneur ne peut finalement pas intervenir : la demande repart en recherche. */
    public function release(Request $request, SosRequest $sos)
    {
        abort_unless($sos->driver_id === $request->user()->id, 403);

        if ($sos->status === 'accepted') {
            $sos->update(['status' => 'searching', 'driver_id' => null]);

            DB::table('sos_request_declines')->insertOrIgnore([
                'sos_request_id' => $sos->id,
                'driver_id'      => $request->user()->id,
            ]);

            $sos->load('user');
            $sos->user->notify(new ClientNotice(
                "Le dépanneur n'a pas pu intervenir. Nous recherchons un autre dépanneur.",
                route('client.sos.show', $sos, false),
            ));
        }

        return redirect()->route('depanneur.requests.index')->with('success', 'Intervention abandonnée.');
    }

    public function history(Request $request)
    {
        return view('tow.jobs.index', [
            'jobs' => SosRequest::with('vehicle', 'user')
                ->where('driver_id', $request->user()->id)
                ->whereIn('status', ['accepted', 'completed'])
                ->latest('updated_at')->get(),
        ]);
    }
}