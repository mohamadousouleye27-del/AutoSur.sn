<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function __construct(private ImageStorage $images) {}

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

        try {
            $publicId = $request->hasFile('photo') ? $this->images->upload($request->file('photo'), 'vehicles') : null;
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['photo' => "L'envoi de la photo a échoué. Réessayez dans un instant."]);
        }

        $vehicle = $request->user()->vehicles()->create(Arr::except($data, ['photo', 'remove_photo']));

        if ($publicId) {
            $vehicle->forceFill(['photo_public_id' => $publicId])->save();
        }

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

        try {
            $newId = $request->hasFile('photo') ? $this->images->upload($request->file('photo'), 'vehicles') : null;
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['photo' => "L'envoi de la photo a échoué. Réessayez dans un instant."]);
        }

        $vehicle->update(Arr::except($data, ['photo', 'remove_photo']));

        if ($newId) {
            $this->deletePhoto($vehicle);
            $vehicle->forceFill(['photo_public_id' => $newId, 'photo_path' => null])->save();
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($vehicle);
            $vehicle->forceFill(['photo_public_id' => null, 'photo_path' => null])->save();
        }

        return redirect()->route('client.vehicles.index')->with('success', 'Véhicule mis à jour.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        $this->deletePhoto($vehicle);
        $vehicle->delete();

        return redirect()->route('client.vehicles.index')->with('success', 'Véhicule supprimé.');
    }

    private function authorizeOwner(Vehicle $vehicle): void
    {
        abort_unless($vehicle->user_id === auth()->id(), 403);
    }

    /** Supprime la photo actuelle, qu'elle soit sur Cloudinary ou (ancienne) en local. */
    private function deletePhoto(Vehicle $vehicle): void
    {
        if ($vehicle->photo_public_id) {
            $this->images->delete($vehicle->photo_public_id);
        }
        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
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
            'photo.uploaded' => "Échec de l'envoi : la photo est trop lourde (2 Mo maximum).",
        ]);
    }
}