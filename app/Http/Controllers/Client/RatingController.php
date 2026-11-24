<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InspectionAppointment;
use App\Models\SosRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function inspection(Request $request, InspectionAppointment $inspection)
    {
        abort_unless($inspection->user_id === $request->user()->id, 403);

        if ($inspection->status !== 'completed' || ! $inspection->garage_id) {
            return back()->withErrors(['rating' => 'Vous pourrez noter le garage une fois la visite effectuée.']);
        }

        return $this->store($request, $inspection, $inspection->garage_id, route('client.inspections.index'));
    }

    public function sos(Request $request, SosRequest $sos)
    {
        abort_unless($sos->user_id === $request->user()->id, 403);

        if ($sos->status !== 'completed' || ! $sos->driver_id) {
            return back()->withErrors(['rating' => "Vous pourrez noter le dépanneur une fois l'intervention terminée."]);
        }

        return $this->store($request, $sos, $sos->driver_id, route('client.sos.show', $sos));
    }

    private function store(Request $request, Model $ratable, int $partnerId, string $redirect)
    {
        if ($ratable->rating()->exists()) {
            return back()->withErrors(['rating' => 'Vous avez déjà donné votre avis.']);
        }

        $data = $request->validate([
            'score'   => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'score.required' => 'Choisissez une note de 1 à 5.',
        ]);

        try {
            $ratable->rating()->create([
                'user_id'    => $request->user()->id,
                'partner_id' => $partnerId,
                'score'      => $data['score'],
                'comment'    => $data['comment'] ?? null,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            return back()->withErrors(['rating' => 'Vous avez déjà donné votre avis.']);
        }

        return redirect($redirect)->with('success', 'Merci pour votre avis !');
    }
}