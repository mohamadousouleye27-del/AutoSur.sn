@extends('layouts.public')
@section('title', 'Connexion : AutoSur.sn')

@section('content')
<section class="mx-auto max-w-md px-6 py-10">
  <div class="card !p-8">
    <h1 class="text-3xl font-extrabold text-brand-700">Connexion</h1>
    <p class="mt-1 text-brand-900/70">Heureux de vous revoir 👋</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <input type="text" name="login" value="{{ old('login') }}" placeholder="Téléphone ou e-mail" class="input" required autofocus>
        @error('login') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <input type="password" name="password" placeholder="Mot de passe" class="input" required>
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="remember" class="rounded border-brand-700/30"> Se souvenir de moi
      </label>
      <button class="btn-primary w-full">Se connecter</button>
    </form>

    <p class="mt-6 text-center text-sm text-brand-900/70">
      Pas encore de compte ? <a href="{{ route('register') }}" class="font-semibold text-brand-700 underline">S'inscrire</a>
    </p>
  </div>
</section>
@endsection