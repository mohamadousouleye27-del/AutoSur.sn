<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function __construct(private OtpService $otp) {}

    public function show(Request $request)
    {
        $user = $this->pending($request);

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.twofa', ['phone' => $user->phone]);
    }

    public function verify(Request $request)
    {
        $user = $this->pending($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'digits:6']]);

        $status = $this->otp->check($user, 'login', $request->input('code'));

        if ($status === 'locked') {
            return back()->withErrors(['code' => 'Trop de tentatives. Réessayez dans quelques minutes.']);
        }
        if ($status !== 'ok') {
            return back()->withErrors(['code' => 'Code incorrect ou expiré.']);
        }

        $remember = (bool) $request->session()->pull('twofa_remember', false);
        $request->session()->forget('twofa_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRoute()));
    }

    public function resend(Request $request)
    {
        $user = $this->pending($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->otp->canSend($user, 'login')) {
            return back()->withErrors(['code' => 'Patientez quelques minutes avant de redemander un code.']);
        }

        $this->otp->issue($user, 'login');

        return back()->with('status', 'Un nouveau code a été envoyé.');
    }

    private function pending(Request $request): ?User
    {
        $id = $request->session()->get('twofa_user_id');

        return $id ? User::where('id', $id)->where('is_active', true)->first() : null;
    }
}