<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SosRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SosController extends Controller
{
    public function index(Request $request)
    {
        $requests = $request->user()->sosRequests()->with('vehicle')->latest()->get();

        return view('client.sos.index', [
            'active'  => $requests->first(fn ($r) => $r->isActive()),
            'history' => $requests,
        ]);
    }

    public function create(Request $request)
    {
        $active = $request->user()->sosRequests()->whereIn('status', ['searching', 'accepted'])->latest()->first();
        if ($active) {
            return redirect()->route('client.sos.show', $active);
        }

        return view('client.sos.create', [
            'vehicles' => $request->user()->vehicles()->orderBy('brand')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $active = $request->user()->sosRequests()->whereIn('status', ['searching', 'accepted'])->latest()->first();
        if ($active) {
            return redirect()->route('client.sos.show', $active);
        }

        $data = $request->validate([
            'vehicle_id'  => ['nullable', Rule::exists('vehicles', 'id')->where('user_id', $request->user()->id)],
            'problem'     => ['required', Rule::in(array_keys(SosRequest::PROBLEMS))],
            'description' => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'landmark'    => ['nullable', 'string', 'max:150'],
        ], [
            'problem.required' => 'Indiquez le type de problème.',
        ]);

        if (empty($data['latitude']) && empty($data['landmark'])) {
            return back()->withInput()->withErrors([
                'landmark' => 'Activez la localisation ou décrivez précisément où vous êtes.',
            ]);
        }

        $sos = $request->user()->sosRequests()->create($data);

        return redirect()->route('client.sos.show', $sos);
    }

    public function show(SosRequest $sos)
    {
        $this->authorizeOwner($sos);

        return view('client.sos.show', ['sos' => $sos->load('vehicle', 'driver')]);
    }

    /** Utilisé par la page de suivi (actualisation automatique). */
    public function status(SosRequest $sos)
    {
        $this->authorizeOwner($sos);
        $sos->load('driver');

        return response()->json([
            'status' => $sos->status,
            'label'  => $sos->badge()['label'],
            'driver' => $sos->driver ? ['name' => $sos->driver->name, 'phone' => $sos->driver->phone] : null,
        ]);
    }

    public function destroy(SosRequest $sos)
    {
        $this->authorizeOwner($sos);

        if ($sos->isActive()) {
            $sos->update(['status' => 'cancelled']);
        }

        return redirect()->route('client.sos.index')->with('success', 'Demande SOS annulée.');
    }

    private function authorizeOwner(SosRequest $sos): void
    {
        abort_unless($sos->user_id === auth()->id(), 403);
    }
}