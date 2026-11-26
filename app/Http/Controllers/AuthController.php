<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use App\Services\OtpService;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private OtpService $otp) {}

    /* ---------- Inscription ---------- */

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->merge([
            'phone' => Phone::normalize($request->input('phone')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'phone'      => ['required', 'regex:/^\+2217[05678]\d{7}$/', 'unique:users,phone'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'       => ['required', 'in:client,garage,depanneur,assurance'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ], [
            'phone.regex'    => 'Numéro invalide. Exemple : 77 123 45 67.',
            'phone.unique'   => 'Ce numéro est déjà utilisé.',
            'email.required' => "L'adresse e-mail est obligatoire : c'est là que nous envoyons votre code de vérification.",
            'email.email'    => 'Adresse e-mail invalide.',
            'email.unique'   => 'Cette adresse e-mail est déjà utilisée.',
        ]);

        $user = new User();
        $user->forceFill([
            'name'           => $data['first_name'] . ' ' . $data['last_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'role'           => $data['role'],
            'password'       => Hash::make($data['password']),
            'partner_status' => in_array($data['role'], User::PARTNER_ROLES, true) ? 'pending' : null,
        ])->save();

        $request->session()->put('otp_user_id', $user->id);
        $this->sendCode($user);

        return redirect()->route('otp.show');
    }

    /* ---------- Vérification du code (e-mail) ---------- */

    public function showOtp(Request $request)
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.otp', ['email' => $user->maskedEmail()]);
    }

    public function verifyOtp(Request $request)
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'digits:6']]);

        $status = $this->otp->check($user, 'register', $request->input('code'));

        if ($status === 'locked') {
            return back()->withErrors(['code' => 'Trop de tentatives. Réessayez dans quelques minutes.']);
        }
        if ($status !== 'ok') {
            return back()->withErrors(['code' => 'Code incorrect ou expiré.']);
        }

        // « phone_verified_at » signifie désormais « compte vérifié » (l'e-mail a été confirmé par le code).
        $user->forceFill(['phone_verified_at' => now(), 'email_verified_at' => now()])->save();

        $request->session()->forget('otp_user_id');
        Auth::login($user);
        $request->session()->regenerate();

        // E-mail de bienvenue, envoyé après la réponse : un échec ne bloque jamais l'utilisateur.
        defer(function () use ($user) {
            try {
                Mail::to($user->email)->send(new WelcomeMail($user, route('login')));
            } catch (\Throwable $e) {
                report($e);
            }
        });

        return redirect()->route($user->homeRoute());
    }

    public function resendOtp(Request $request)
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->otp->canSend($user, 'register')) {
            return back()->withErrors(['code' => 'Patientez quelques minutes avant de redemander un code.']);
        }

        $this->sendCode($user);

        return back()->with('status', 'Un nouveau code a été envoyé.');
    }

    /* ---------- Connexion / Déconnexion ---------- */

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($request->input('login'));
        $user = str_contains($login, '@')
            ? User::where('email', strtolower($login))->first()
            : User::where('phone', Phone::normalize($login))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['login' => 'Identifiants incorrects.'])->onlyInput('login');
        }

        if (! $user->is_active) {
            return back()->withErrors(['login' => 'Ce compte est suspendu. Contactez AutoSur.'])->onlyInput('login');
        }

        if (! $user->phone_verified_at) {
            if (! $user->email) {
                return back()->withErrors(['login' => "Ce compte n'est pas vérifié et n'a pas d'adresse e-mail. Contactez AutoSur."])->onlyInput('login');
            }

            $request->session()->put('otp_user_id', $user->id);
            $this->sendCode($user);

            return redirect()->route('otp.show');
        }

        // Double authentification : le code est envoyé par e-mail avant de connecter l'utilisateur.
        if ($user->two_factor_enabled && $user->email) {
            $this->otp->issue($user, 'login');
            $request->session()->put('twofa_user_id', $user->id);
            $request->session()->put('twofa_remember', $request->boolean('remember'));

            return redirect()->route('twofa.show');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRoute()));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /* ---------- Outils privés ---------- */

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('otp_user_id');

        return $id ? User::find($id) : null;
    }

    private function sendCode(User $user): void
    {
        if (! $this->otp->issue($user, 'register')) {
            session()->flash('otp_error', "L'e-mail n'a pas pu être envoyé. Vérifiez l'adresse, puis cliquez sur « Renvoyer le code ».");
        }
    }
}