<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecurityController extends Controller
{
    public function edit()
    {
        return view('security.edit', ['mailReady' => $this->mailReady()]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ], [
            'current_password.current_password' => 'Mot de passe actuel incorrect.',
            'password.different'                => "Le nouveau mot de passe doit être différent de l'actuel.",
        ]);

        $user = $request->user();
        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        // Les autres appareils sont déconnectés.
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('success', 'Mot de passe modifié. Les autres appareils ont été déconnectés.');
    }

    public function toggleTwoFactor(Request $request)
    {
        $request->validateWithBag('twofa', [
            'current_password' => ['required', 'current_password'],
        ], [
            'current_password.current_password' => 'Mot de passe incorrect.',
        ]);

        $user = $request->user();

        if (! $user->two_factor_enabled) {
            if (! $user->email || ! $user->email_verified_at) {
                return back()->withErrors([
                    'two_factor' => "Votre compte n'a pas d'adresse e-mail confirmée : c'est là que le code est envoyé.",
                ]);
            }
            if (! $this->mailReady()) {
                return back()->withErrors([
                    'two_factor' => "La double authentification sera disponible dès que l'envoi d'e-mails sera configuré sur la plateforme.",
                ]);
            }
        }

        $user->forceFill(['two_factor_enabled' => ! $user->two_factor_enabled])->save();

        return back()->with('success', $user->two_factor_enabled
            ? 'Double authentification activée.'
            : 'Double authentification désactivée.');
    }

    /** Évite de verrouiller des comptes quand aucun e-mail ne peut réellement partir. */
    private function mailReady(): bool
    {
        return app()->environment('local') || ! in_array(config('mail.default'), ['log', 'array'], true);
    }
}