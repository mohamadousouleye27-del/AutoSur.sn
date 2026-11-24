<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use App\Services\OtpService;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    /* ---------- Inscription ---------- */

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->merge(['phone' => $this->normalizePhone($request->input('phone'))]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'phone'      => ['required', 'regex:/^\+2217[05678]\d{7}$/', 'unique:users,phone'],
            'email'      => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'role'       => ['required', 'in:client,garage,depanneur,assurance'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ], [
            'phone.regex'  => 'Numéro invalide. Exemple : 77 123 45 67.',
            'phone.unique' => 'Ce numéro est déjà utilisé.',
        ]);

        $user = new User();
        $user->forceFill([
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
            'phone'    => $data['phone'],
            'email'    => $data['email'] ?? null,
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
            'partner_status' => in_array($data['role'], \App\Models\User::PARTNER_ROLES, true) ? 'pending' : null,
        ])->save();

        $this->sendOtp($user);
        $request->session()->put('otp_user_id', $user->id);

                // E-mail de bienvenue (si une adresse a été donnée), envoyé après la réponse :
        // un échec d'envoi ne bloque jamais l'inscription.
        if ($user->email) {
            $verifyUrl = URL::temporarySignedRoute('email.verify', now()->addDays(3), [
                'user' => $user->id,
                'hash' => sha1($user->email),
            ]);

            defer(function () use ($user, $verifyUrl) {
                try {
                    Mail::to($user->email)->send(new WelcomeMail($user, $verifyUrl));
                } catch (\Throwable $e) {
                    report($e);
                }
            });
        }

        return redirect()->route('otp.show');
    }

    /* ---------- Vérification OTP ---------- */

    public function showOtp(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.otp', ['phone' => $user->phone]);
    }

    public function verifyOtp(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'digits:6']]);

        $key = 'otp-verify:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'code' => 'Trop de tentatives. Réessayez dans ' . RateLimiter::availableIn($key) . ' secondes.',
            ]);
        }

        $hash = Cache::get('otp:' . $user->id);
        if (! $hash || ! Hash::check($request->input('code'), $hash)) {
            RateLimiter::hit($key, 300);
            return back()->withErrors(['code' => 'Code incorrect ou expiré.']);
        }

        RateLimiter::clear($key);
        Cache::forget('otp:' . $user->id);
        $user->forceFill(['phone_verified_at' => now()])->save();

        $request->session()->forget('otp_user_id');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRoute());
    }

    public function resendOtp(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('login');
        }

        $key = 'otp-send:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['code' => 'Patientez quelques minutes avant de redemander un code.']);
        }
        RateLimiter::hit($key, 300);

        $this->sendOtp($user);

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
            ? User::where('email', $login)->first()
            : User::where('phone', $this->normalizePhone($login))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['login' => 'Identifiants incorrects.'])->onlyInput('login');
        }

        if (! $user->is_active) {
            return back()->withErrors(['login' => 'Ce compte est suspendu. Contactez AutoSur.'])->onlyInput('login');
        }

        if (! $user->phone_verified_at) {
            $this->sendOtp($user);
            $request->session()->put('otp_user_id', $user->id);
            return redirect()->route('otp.show');
        }

        // Double authentification : on ne connecte l'utilisateur qu'après le code SMS.
        if ($user->two_factor_enabled) {
            app(OtpService::class)->issue($user, 'login');
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

    private function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        if (str_starts_with($digits, '00221')) {
            $digits = substr($digits, 5);
        } elseif (str_starts_with($digits, '221') && strlen($digits) === 12) {
            $digits = substr($digits, 3);
        }

        return '+221' . $digits;
    }

    private function sendOtp(User $user): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put('otp:' . $user->id, Hash::make($code), now()->addMinutes(5));

        // TODO : brancher la passerelle SMS locale ici.
        Log::info("OTP AutoSur pour {$user->phone} : {$code}");

        if (app()->environment('local')) {
            session()->flash('dev_otp', $code);
        }
    }
}