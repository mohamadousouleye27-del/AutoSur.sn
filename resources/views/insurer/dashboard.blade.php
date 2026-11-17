@extends('layouts.app')
@section('title', 'Espace assureur')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Bonjour, {{ auth()->user()->name }}</h1>
<p class="mt-1 text-brand-900/70">Vos demandes de devis, offres et commissions.</p>

<div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
  <a href="{{ route('assurance.requests.index') }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Nouvelles demandes</p>
    <p class="mt-2 text-4xl font-extrabold {{ $newRequests ? 'text-amber-600' : 'text-brand-700' }}">{{ $newRequests }}</p>
  </a>
  <a href="{{ route('assurance.offers.index') }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Offres en attente</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $pending }}</p>
  </a>
  <a href="{{ route('assurance.subscriptions.index') }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Souscriptions</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $accepted }}</p>
  </a>
  <div class="card">
    <p class="text-sm font-semibold text-brand-900/60">Commissions estimées</p>
    <p class="mt-2 text-3xl font-extrabold text-brand-700">{{ number_format($commission, 0, ',', ' ') }} <span class="text-base">FCFA</span></p>
  </div>
</div>

<div class="card mt-8 max-w-xl">
  <h2 class="text-lg font-extrabold">Répondre aux demandes</h2>
  <p class="mt-1 text-sm text-brand-900/70">Des automobilistes attendent une offre pour leur véhicule.</p>
  <a href="{{ route('assurance.requests.index') }}" class="btn-primary mt-4">Voir les demandes</a>
</div>
@endsection