@extends('layouts.app')
@section('title', 'Demandes de devis')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Demandes de devis</h1>
<p class="mt-1 text-brand-900/70">{{ $quotes->count() }} demande(s) ouverte(s)</p>

@if ($quotes->isEmpty())
  <div class="card mt-8 py-16 text-center text-sm text-brand-900/60">Aucune demande ouverte pour le moment.</div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($quotes as $q)
      <a href="{{ route('assurance.requests.show', $q) }}" class="card flex flex-wrap items-center justify-between gap-4 transition hover:shadow-md">
        <div>
          <p class="text-lg font-extrabold text-brand-900">{{ $q->vehicle->brand }} {{ $q->vehicle->model }} {{ $q->vehicle->year }}</p>
          <p class="text-sm text-brand-900/60">{{ $q->coverageLabel() }} · {{ $q->usageLabel() }}</p>
          <p class="text-xs text-brand-900/40">Reçue {{ $q->created_at->locale('fr')->diffForHumans() }}</p>
        </div>
        @if ($q->my_offers_count)
          <span class="badge">Offre envoyée</span>
        @else
          <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">À traiter</span>
        @endif
      </a>
    @endforeach
  </div>
@endif
@endsection