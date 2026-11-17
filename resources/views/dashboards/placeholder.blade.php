@extends('layouts.public')
@section('title', 'Espace ' . $space)

@section('content')
<section class="mx-auto max-w-6xl px-6 py-16">
  <span class="badge">Espace {{ $space }}</span>
  <h1 class="mt-4 text-4xl font-extrabold text-brand-700">Bienvenue, {{ auth()->user()->name }} 👋</h1>
  <p class="mt-3 text-brand-900/70">Cette interface sera construite dans les prochaines étapes.</p>
</section>
@endsection