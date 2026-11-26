@extends('layouts.app')
@section('title', 'Sécurité du compte')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Sécurité du compte</h1>
<p class="mt-1 text-brand-900/70">Protégez l'accès à vos véhicules et à vos documents.</p>

{{-- Mot de passe --}}
<section class="card mt-8 max-w-2xl !p-8">
  <h2 class="text-lg font-extrabold">Changer le mot de passe</h2>

  <form method="POST" action="{{ route('security.password') }}" class="mt-4 space-y-4">
    @csrf
    <div>
      <input type="password" name="current_password" placeholder="Mot de passe actuel" class="input" required>
      @error('current_password', 'password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
      <input type="password" name="password" placeholder="Nouveau mot de passe (8 caractères min.)" class="input" required>
      @error('password', 'password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <input type="password" name="password_confirmation" placeholder="Confirmer le nouveau mot de passe" class="input" required>
    <button class="btn-primary">Modifier le mot de passe</button>
  </form>
</section>

{{-- Double authentification --}}
<section class="card mt-6 max-w-2xl !p-8">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-lg font-extrabold">Double authentification (e-mail)</h2>
    @if (auth()->user()->two_factor_enabled)
      <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Activée</span>
    @else
      <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">Désactivée</span>
    @endif
  </div>

  <p class="mt-2 text-sm text-brand-900/70">
    À chaque connexion, un code envoyé par e-mail à {{ auth()->user()->maskedEmail() ?: "l'adresse de votre compte" }} est demandé en plus du mot de passe.
  </p>

  @if (! $mailReady && ! auth()->user()->two_factor_enabled)
    <p class="mt-3 rounded-2xl bg-amber-100 px-4 py-3 text-sm font-semibold text-amber-800">
      Bientôt disponible : cette option sera activable dès que l'envoi d'e-mails sera en service.
    </p>
  @endif
  @endif

  @error('two_factor')
    <p class="mt-3 rounded-2xl bg-red-100 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</p>
  @enderror

  <form method="POST" action="{{ route('security.twofa') }}" class="mt-4 flex flex-wrap items-end gap-3">
    @csrf
    <div class="min-w-[14rem] flex-1">
      <input type="password" name="current_password" placeholder="Confirmez avec votre mot de passe" class="input" required>
      @error('current_password', 'twofa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <button class="{{ auth()->user()->two_factor_enabled ? 'btn-accent' : 'btn-primary' }}">
      {{ auth()->user()->two_factor_enabled ? 'Désactiver' : 'Activer' }}
    </button>
  </form>
</section>
@endsection