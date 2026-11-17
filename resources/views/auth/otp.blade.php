@extends('layouts.public')
@section('title', 'Vérification : AutoSur.sn')

@section('content')
<section class="mx-auto max-w-md px-6 py-10">
  <div class="card !p-8 text-center">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-200 text-3xl">📱</div>
    <h1 class="mt-4 text-2xl font-extrabold text-brand-700">Vérifiez votre numéro</h1>
    <p class="mt-2 text-sm text-brand-900/70">Entrez le code à 6 chiffres envoyé au <strong>{{ $phone }}</strong>.</p>

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