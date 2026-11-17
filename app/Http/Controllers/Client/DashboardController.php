<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->get();
        $alerts = collect();

        $fields = [
            'insurance_expires_at'       => 'Assurance',
            'technical_visit_expires_at' => 'Visite technique',
        ];

        foreach ($vehicles as $vehicle) {
            foreach ($fields as $field => $label) {
                $status = $vehicle->status($field);
                if (in_array($status['label'], ['Expirée', 'Expire bientôt'], true)) {
                    $alerts->push([
                        'title'    => $label . ' · ' . $vehicle->brand . ' ' . $vehicle->model,
                        'subtitle' => $vehicle->plate,
                        'date'     => $vehicle->{$field},
                        'status'   => $status,
                    ]);
                }
            }
        }

        $documents = $user->documents()->with('vehicle')->get();
        foreach ($documents->whereNotNull('expires_at') as $doc) {
            $status = $doc->status();
            if (in_array($status['label'], ['Expiré', 'Expire bientôt'], true)) {
                $alerts->push([
                    'title'    => 'Document · ' . $doc->title,
                    'subtitle' => $doc->vehicle?->plate ?? $doc->typeLabel(),
                    'date'     => $doc->expires_at,
                    'status'   => $status,
                ]);
            }
        }

        return view('client.dashboard', [
            'vehicles'       => $vehicles,
            'documentsCount' => $documents->count(),
            'alerts'         => $alerts->sortBy('date')->values(),
        ]);
    }
}