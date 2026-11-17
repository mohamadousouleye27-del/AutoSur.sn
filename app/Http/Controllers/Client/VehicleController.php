<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        return view('client.vehicles.index', [
            'vehicles' => $request->user()->vehicles()->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('client.vehicles.form', ['vehicle' => new Vehicle()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $vehicle = $request->user()->vehicles()->create(Arr::except($data, ['photo', 'remove_photo']));
        $this->savePhoto($request, $vehicle);

        return redirect()->route('client.vehicles.index')->with('success', 'Véhicule ajouté.');
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        return view('client.vehicles.form', ['vehicle' => $vehicle]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        $data = $this->validated($request, $vehicle);

        $vehicle->update(Arr::except($data, ['photo', 'remove_photo']));
        $this->savePhoto($request, $vehicle);

        return redirect()->route('client.vehicles.index')->with('success', 'Véhicule mis à jour.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }
        $vehicle->delete();

        return redirect()->route('client.vehicles.index')->with('success', 'Véhicule supprimé.');
    }

    private function authorizeOwner(Vehicle $vehicle): void
    {
        abort_unless($vehicle->user_id === auth()->id(), 403);
    }

    /** Suppression et/ou remplacement de la photo. */
    private function savePhoto(Request $request, Vehicle $vehicle): void
    {
        if ($request->boolean('remove_photo') && $vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
            $vehicle->photo_path = null;
        }

        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $vehicle->photo_path = $request->file('photo')->store('vehicles', 'public');
        }

        if ($vehicle->isDirty('photo_path')) {
            $vehicle->save();
        }
    }

    private function validated(Request $request, ?Vehicle $vehicle = null): array
    {
        $request->merge(['plate' => strtoupper(trim((string) $request->input('plate')))]);

        return $request->validate([
            'plate' => [
                'required', 'string', 'max:20',
                Rule::unique('vehicles', 'plate')
                    ->where('user_id', auth()->id())
                    ->ignore($vehicle?->id),
            ],
            'brand'                      => ['required', 'string', 'max:60'],
            'model'                      => ['required', 'string', 'max:60'],
            'year'                       => ['nullable', 'integer', 'between:1950,' . (now()->year + 1)],
            'color'                      => ['nullable', 'string', 'max:40'],
            'chassis_number'             => ['nullable', 'string', 'max:40'],
            'insurance_expires_at'       => ['nullable', 'date'],
            'technical_visit_expires_at' => ['nullable', 'date'],
            'photo'                      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo'               => ['nullable', 'boolean'],
        ], [
            'plate.unique'   => 'Ce véhicule est déjà enregistré.',
            'photo.image'    => 'Le fichier doit être une image.',
            'photo.mimes'    => 'Formats acceptés : JPG, PNG ou WebP.',
            'photo.max'      => 'La photo ne doit pas dépasser 2 Mo.',
            'photo.uploaded' => 'Échec de l\'envoi : la photo est trop lourde (2 Mo maximum).',
        ]);
    }
}