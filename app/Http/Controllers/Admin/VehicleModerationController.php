<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleModerationController extends Controller
{
    public function __construct(private ImageStorage $images) {}

    public function index(Request $request)
    {
        $vehicles = Vehicle::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $like = '%' . $request->query('q') . '%';
                $query->where(fn ($w) => $w->where('plate', 'like', $like)
                    ->orWhere('brand', 'like', $like)
                    ->orWhere('model', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('phone', 'like', $like)));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->photo_public_id) {
            $this->images->delete($vehicle->photo_public_id);
        }
        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }

        $vehicle->delete();

        return back()->with('success', 'Véhicule supprimé.');
    }
}