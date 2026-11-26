@extends('layouts.public')
@section('title', 'Mot de passe oublié')

@section('content')
<section class="mx-auto max-w-md px-6 py-10">
  <div class="card !p-8">
    <h1 class="text-2xl font-extrabold text-brand-700">Mot de passe oublié</h1>
    <p class="mt-1 text-sm text-brand-900/70">Entrez votre téléphone ou votre e-mail : nous envoyons un code à l'adresse e-mail de votre compte.</p>

    <form method="POST" action="{{ route('password.send') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <input type="text" name="login" value="{{ old('login') }}" placeholder="Téléphone ou e-mail" class="input" required autofocus>
        @error('login') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <button class="btn-primary w-full">Recevoir le code</button>
    </form>

    <p class="mt-6 text-center text-sm">
      <a href="{{ route('login') }}" class="font-semibold text-brand-700 underline">← Retour à la connexion</a>
    </p>
  </div>
</section>
@endsection