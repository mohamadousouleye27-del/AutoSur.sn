<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class OtpService
{
    /** Génère un code, le conserve (haché) et l'envoie par SMS. */
    public function issue(User $user, string $purpose, int $minutes = 10): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->key($user, $purpose), Hash::make($code), now()->addMinutes($minutes));

        app(SmsGateway::class)->send(
            $user->phone,
            "AutoSur : votre code de vérification est {$code}. Il expire dans {$minutes} minutes."
        );

        if (app()->environment('local')) {
            session()->flash('dev_otp', $code);
        }
    }

    /** @return string 'ok' | 'invalid' | 'locked' */
    public function check(User $user, string $purpose, string $code): string
    {
        $attempts = "otp-check:{$purpose}:{$user->id}";

        if (RateLimiter::tooManyAttempts($attempts, 5)) {
            return 'locked';
        }

        $hash = Cache::get($this->key($user, $purpose));

        if (! $hash || ! Hash::check($code, $hash)) {
            RateLimiter::hit($attempts, 300);
            return 'invalid';
        }

        RateLimiter::clear($attempts);
        Cache::forget($this->key($user, $purpose));

        return 'ok';
    }

    /** Anti-spam : 3 envois maximum toutes les 5 minutes. */
    public function canSend(User $user, string $purpose): bool
    {
        $key = "otp-send:{$purpose}:{$user->id}";

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return false;
        }

        RateLimiter::hit($key, 300);

        return true;
    }

    private function key(User $user, string $purpose): string
    {
        return "otp:{$purpose}:{$user->id}";
    }
}