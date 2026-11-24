@extends('layouts.public')
@section('title', 'Nouveau mot de passe')

@section('content')
<section class="mx-auto max-w-md px-6 py-10">
  <div class="card !p-8">
    <h1 class="text-2xl font-extrabold text-brand-700">Nouveau mot de passe</h1>
    <p class="mt-1 text-sm text-brand-900/70">Saisissez le code reçu par SMS au <strong>{{ $phone }}</strong>.</p>

    @if (session('status'))
      <p class="mt-4 rounded-2xl bg-brand-200 px-4 py-2 text-sm font-medium text-brand-700">{{ session('status') }}</p>
    @endif

    @if (session('dev_otp'))
      <p class="mt-4 rounded-2xl border border-dashed border-brand-700/40 px-4 py-2 text-sm">
        Mode développement : votre code est <strong>{{ session('dev_otp') }}</strong>
      </p>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <input type="text" name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" autofocus
               placeholder="Code à 6 chiffres" class="input text-center text-xl font-bold tracking-[0.4em]">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <input type="password" name="password" placeholder="Nouveau mot de passe (8 caractères min.)" class="input" required>
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" class="input" required>
      <button class="btn-primary w-full">Modifier mon mot de passe</button>
    </form>

    <form method="POST" action="{{ route('password.send') }}" class="mt-4 text-center">
      @csrf
      <input type="hidden" name="phone" value="{{ $phone }}">
      <button class="text-sm font-semibold text-brand-700 underline">Renvoyer le code</button>
    </form>
  </div>
</section>
@endsection