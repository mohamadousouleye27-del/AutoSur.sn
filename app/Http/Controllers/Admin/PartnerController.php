<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $order = ['pending' => 0, 'approved' => 1, 'rejected' => 2];

        $partners = User::whereIn('role', User::PARTNER_ROLES)
            ->latest()->get()
            ->sortBy(fn ($u) => $order[$u->partner_status] ?? 3)
            ->values();

        return view('admin.partners.index', compact('partners'));
    }

    public function approve(User $user)
    {
        $this->ensurePartner($user)->forceFill(['partner_status' => 'approved'])->save();

        return back()->with('success', 'Partenaire validé.');
    }

    public function reject(User $user)
    {
        $this->ensurePartner($user)->forceFill(['partner_status' => 'rejected'])->save();

        return back()->with('success', 'Partenaire refusé : son accès est bloqué.');
    }

    public function commission(Request $request, User $user)
    {
        abort_unless($user->role === 'assurance', 404);

        $data = $request->validate(
            ['commission_rate' => ['required', 'numeric', 'between:5,12']],
            ['commission_rate.between' => 'Le taux doit être compris entre 5 % et 12 %.']
        );

        $user->forceFill(['commission_rate' => $data['commission_rate']])->save();

        return back()->with('success', 'Taux de commission mis à jour (il s\'applique aux prochaines offres).');
    }

    private function ensurePartner(User $user): User
    {
        abort_unless($user->isPartner(), 404);

        return $user;
    }
}