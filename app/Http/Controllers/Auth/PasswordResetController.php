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
        $request->validate(['login' => ['required', 'string', 'max:255']]);

        $identifier = $this->normalize($request->input('login'));
        $user = $this->findUser($identifier);

        if ($user && $user->email && $this->otp->canSend($user, 'reset')) {
            $this->otp->issue($user, 'reset');
        }

        // Même réponse que le compte existe ou non.
        $request->session()->put('reset_login', $identifier);

        return redirect()->route('password.code')
            ->with('status', "Si ce compte existe, un code vient d'être envoyé à son adresse e-mail.");
    }

    public function showReset(Request $request)
    {
        if (! $request->session()->has('reset_login')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset', ['login' => $request->session()->get('reset_login')]);
    }

    public function reset(Request $request)
    {
        $identifier = $request->session()->get('reset_login');

        if (! $identifier) {
            return redirect()->route('password.forgot');
        }

        $data = $request->validate([
            'code'     => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user   = $this->findUser($identifier);
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
        $request->session()->forget('reset_login');

        return redirect()->route('login')->with('status', 'Mot de passe modifié. Vous pouvez vous connecter.');
    }

    private function normalize(string $login): string
    {
        $login = trim($login);

        return str_contains($login, '@') ? strtolower($login) : Phone::normalize($login);
    }

    private function findUser(string $identifier): ?User
    {
        return User::where('is_active', true)
            ->where(str_contains($identifier, '@') ? 'email' : 'phone', $identifier)
            ->first();
    }
}