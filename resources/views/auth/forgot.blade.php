@extends('layouts.public')
@section('title', 'Mot de passe oublié')

@section('content')
<section class="mx-auto max-w-md px-6 py-10">
  <div class="card !p-8">
    <h1 class="text-2xl font-extrabold text-brand-700">Mot de passe oublié</h1>
    <p class="mt-1 text-sm text-brand-900/70">Entrez votre numéro de téléphone : nous vous envoyons un code par SMS.</p>

    <form method="POST" action="{{ route('password.send') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Téléphone (ex : 77 123 45 67)" class="input" required autofocus>
        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <button class="btn-primary w-full">Recevoir le code</button>
    </form>

    <p class="mt-6 text-center text-sm">
      <a href="{{ route('login') }}" class="font-semibold text-brand-700 underline">← Retour à la connexion</a>
    </p>
  </div>
</section>
@endsection