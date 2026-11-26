@extends('layouts.public')
@section('title', 'Vérification : AutoSur')

@section('content')
<section class="mx-auto max-w-md px-6 py-10">
  <div class="card !p-8 text-center">
    <h1 class="text-2xl font-extrabold text-brand-700">Vérifiez votre adresse e-mail</h1>
    <p class="mt-2 text-sm text-brand-900/70">Entrez le code à 6 chiffres envoyé à <strong>{{ $email }}</strong>.</p>
    <p class="mt-1 text-xs text-brand-900/50">Rien reçu après 2 minutes ? Regardez dans vos courriers indésirables.</p>

    @if (session('otp_error'))
      <p class="mt-4 rounded-2xl bg-red-100 px-4 py-2 text-sm font-medium text-red-700">{{ session('otp_error') }}</p>
    @endif

    @if (session('status'))
      <p class="mt-4 rounded-2xl bg-brand-200 px-4 py-2 text-sm font-medium text-brand-700">{{ session('status') }}</p>
    @endif

    @if (session('dev_otp'))
      <p class="mt-4 rounded-2xl border border-dashed border-brand-700/40 px-4 py-2 text-sm">
        Mode développement : votre code est <strong>{{ session('dev_otp') }}</strong>
      </p>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}" class="mt-6 space-y-4">
      @csrf
      <input type="text" name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" autofocus
             placeholder="••••••" class="input text-center text-2xl font-bold tracking-[0.5em]">
      @error('code') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      <button class="btn-primary w-full">Valider</button>
    </form>

    <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
      @csrf
      <button class="text-sm font-semibold text-brand-700 underline">Renvoyer le code</button>
    </form>
  </div>
</section>
@endsection