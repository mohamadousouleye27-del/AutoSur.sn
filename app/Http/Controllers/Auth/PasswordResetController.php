<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function __construct(private OtpService $otp) {}

    public function showRequest()
    {
        return view('auth.forgot');
    }

    public function sendCode(Request $request)
    {
        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);

        $request->validate(
            ['phone' => ['required', 'regex:/^\+2217[05678]\d{7}$/']],
            ['phone.regex' => 'Numéro invalide. Exemple : 77 123 45 67.']
        );

        $phone = $request->input('phone');
        $user  = User::where('phone', $phone)->where('is_active', true)->first();

        if ($user && $this->otp->canSend($user, 'reset')) {
            $this->otp->issue($user, 'reset');
        }

        // Même réponse que le numéro existe ou non.
        $request->session()->put('reset_phone', $phone);

        return redirect()->route('password.code')
            ->with('status', "Si ce numéro correspond à un compte, un code vient d'être envoyé par SMS.");
    }

    public function showReset(Request $request)
    {
        if (! $request->session()->has('reset_phone')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset', ['phone' => $request->session()->get('reset_phone')]);
    }

    public function reset(Request $request)
    {
        $phone = $request->session()->get('reset_phone');

        if (! $phone) {
            return redirect()->route('password.forgot');
        }

        $data = $request->validate([
            'code'     => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user   = User::where('phone', $phone)->where('is_active', true)->first();
        $status = $user ? $this->otp->check($user, 'reset', $data['code']) : 'invalid';

        if ($status === 'locked') {
            return back()->withErrors(['code' => 'Trop de tentatives. Réessayez dans quelques minutes.']);
        }
        if ($status !== 'ok') {
            return back()->withErrors(['code' => 'Code incorrect ou expiré.']);
        }

        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        // Toutes les sessions ouvertes de ce compte sont fermées.
        DB::table('sessions')->where('user_id', $user->id)->delete();
        $request->session()->forget('reset_phone');

        return redirect()->route('login')->with('status', 'Mot de passe modifié. Vous pouvez vous connecter.');
    }
}