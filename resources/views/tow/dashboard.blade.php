@extends('layouts.app')
@section('title', 'Espace dépanneur')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Bonjour, {{ auth()->user()->name }}</h1>

<div class="card mt-6 flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="font-extrabold text-brand-900">
      Statut :
      <span class="{{ $available ? 'text-green-600' : 'text-slate-500' }}">{{ $available ? 'Disponible' : 'Indisponible' }}</span>
    </p>
    <p class="text-sm text-brand-900/60">{{ $available ? 'Vous recevez les demandes SOS.' : 'Passez en disponible pour recevoir des demandes.' }}</p>
  </div>
  <form method="POST" action="{{ route('depanneur.availability') }}">
    @csrf
    <button class="{{ $available ? 'btn-accent' : 'btn-primary' }}">{{ $available ? 'Me mettre indisponible' : 'Me mettre disponible' }}</button>
  </form>
</div>

<div class="mt-6 grid gap-5 sm:grid-cols-3">
  <a href="{{ route('depanneur.requests.index') }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Demandes ouvertes</p>
    <p class="mt-2 text-4xl font-extrabold {{ $openCount ? 'text-red-600' : 'text-brand-700' }}">{{ $openCount }}</p>
  </a>
  <div class="card">
    <p class="text-sm font-semibold text-brand-900/60">Intervention en cours</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $active ? 1 : 0 }}</p>
  </div>
  <a href="{{ route('depanneur.jobs.index') }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Interventions terminées</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $doneCount }}</p>
  </a>
    <div class="card">
    <p class="text-sm font-semibold text-brand-900/60">Note moyenne</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ ($avg = auth()->user()->averageRating()) ? number_format($avg, 1, ',', '') . ' / 5' : '—' }}</p>
  </div>
</div>

@if ($active)
  <a href="{{ route('depanneur.requests.show', $active) }}" class="card mt-6 flex flex-wrap items-center justify-between gap-3 !border-amber-300 transition hover:shadow-md">
    <div>
      <p class="font-extrabold">Intervention en cours : {{ $active->problemLabel() }}</p>
      <p class="text-sm text-brand-900/60">{{ $active->vehicle ? $active->vehicle->brand . ' ' . $active->vehicle->model : 'Véhicule non précisé' }}</p>
    </div>
    <span class="btn-primary !py-2 text-sm">Ouvrir</span>
  </a>
@endif
@endsection