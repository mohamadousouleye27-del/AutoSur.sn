<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /** Lien signé (temporaire et infalsifiable) envoyé dans l'e-mail de bienvenue. */
    public function verify(Request $request, User $user, string $hash)
    {
        abort_unless($user->email && hash_equals(sha1($user->email), $hash), 403);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()->route('login')->with('status', 'Adresse e-mail confirmée. Merci !');
    }
}