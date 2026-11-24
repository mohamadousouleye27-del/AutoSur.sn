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
        return view('security.edit', ['smsReady' => $this->smsReady()]);
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

        if (! $user->two_factor_enabled && ! $this->smsReady()) {
            return back()->withErrors([
                'two_factor' => "La double authentification sera disponible dès que l'envoi de SMS sera activé sur la plateforme.",
            ]);
        }

        $user->forceFill(['two_factor_enabled' => ! $user->two_factor_enabled])->save();

        return back()->with('success', $user->two_factor_enabled
            ? 'Double authentification activée.'
            : 'Double authentification désactivée.');
    }

    /** Évite de verrouiller des comptes quand aucun SMS ne peut réellement partir. */
    private function smsReady(): bool
    {
        return app()->environment('local') || config('alerts.sms_driver') !== 'log';
    }
}