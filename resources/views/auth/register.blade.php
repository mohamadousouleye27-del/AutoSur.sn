@extends('layouts.public')
@section('title', 'Inscription : AutoSur.sn')

@section('content')
<section class="mx-auto max-w-xl px-6 py-10">
  <div class="card !p-8">
    <h1 class="text-3xl font-extrabold text-brand-700">Créer mon compte</h1>
    <p class="mt-1 text-brand-900/70">Un code de vérification sera envoyé à votre adresse e-mail.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
      @csrf

      <div>
        <p class="mb-2 text-sm font-semibold">Je suis…</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach (['client' => '🚘 Automobiliste', 'garage' => '🔧 Garage', 'depanneur' => '🆘 Dépanneur', 'assurance' => '🛡️ Assurance'] as $value => $label)
            <label class="cursor-pointer">
              <input type="radio" name="role" value="{{ $value }}" class="peer sr-only" @checked(old('role', 'client') === $value)>
              <span class="block rounded-2xl border border-brand-700/20 px-4 py-3 text-center text-sm font-semibold peer-checked:border-brand-700 peer-checked:bg-brand-200">{{ $label }}</span>
            </label>
          @endforeach
        </div>
        @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Prénom" class="input" required>
          @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
          <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Nom" class="input" required>
          @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
      </div>

      <div>
        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Téléphone (ex : 77 123 45 67)" class="input" required>
        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Adresse e-mail (pour recevoir vos codes)" class="input" required>
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <input type="password" name="password" placeholder="Mot de passe (8 caractères min.)" class="input" required>
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" class="input" required>

      <button class="btn-primary w-full">Recevoir mon code</button>
    </form>

    <p class="mt-6 text-center text-sm text-brand-900/70">
      Déjà un compte ? <a href="{{ route('login') }}" class="font-semibold text-brand-700 underline">Se connecter</a>
    </p>
  </div>
</section>
@endsection