@extends('layouts.app')
@section('title', 'Demande SOS')

@section('content')
@php $b = $sos->badge(); @endphp

<a href="{{ route('depanneur.requests.index') }}" class="text-sm font-semibold text-brand-700 underline">← Demandes</a>

<div class="mt-3 flex flex-wrap items-center justify-between gap-3">
  <h1 class="text-3xl font-extrabold text-brand-700">{{ $sos->problemLabel() }}</h1>
  <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
</div>

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

<div class="mt-6 grid gap-6 lg:grid-cols-2">
  <section class="card">
    <dl class="space-y-3 text-sm">
      <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Reçue</dt><dd class="font-bold">{{ $sos->created_at->locale('fr')->translatedFormat('d F Y à H:i') }}</dd></div>
      <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Véhicule</dt>
        <dd class="text-right font-bold">{{ $sos->vehicle ? $sos->vehicle->brand . ' ' . $sos->vehicle->model . ' · ' . $sos->vehicle->color : 'Non précisé' }}</dd></div>
      @if ($sos->vehicle?->plate)
        <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Immatriculation</dt><dd class="font-bold">{{ $sos->vehicle->plate }}</dd></div>
      @endif
      @if ($sos->landmark)
        <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Repère</dt><dd class="text-right font-bold">{{ $sos->landmark }}</dd></div>
      @endif
      @if ($sos->description)
        <div><dt class="text-brand-900/60">Détails</dt><dd>{{ $sos->description }}</dd></div>
      @endif
    </dl>

    <div class="mt-5 rounded-2xl bg-brand-400/15 p-4 text-sm">
      <p class="font-bold">Client</p>
      @if ($mine)
        <p>{{ $sos->user->name }}</p>
        <a href="tel:{{ $sos->user->phone }}" class="font-bold text-brand-700 underline">{{ $sos->user->phone }}</a>
      @else
        <p>{{ \Illuminate\Support\Str::before($sos->user->name, ' ') }}</p>
        <p class="text-xs text-brand-900/60">Le téléphone est communiqué dès que vous acceptez la demande.</p>
      @endif
    </div>

    <div class="mt-5 flex flex-wrap gap-3">
      @if ($mine && $sos->status === 'accepted')
        @if ($sos->directionsUrl())
          <a href="{{ $sos->directionsUrl() }}" target="_blank" rel="noopener" class="btn-accent">Itinéraire</a>
        @endif
        <form method="POST" action="{{ route('depanneur.requests.complete', $sos) }}" onsubmit="return confirm('Marquer l\'intervention comme terminée ?')">
          @csrf <button class="btn-primary">Intervention terminée</button>
        </form>
        <form method="POST" action="{{ route('depanneur.requests.release', $sos) }}" onsubmit="return confirm('Abandonner cette intervention ?')">
          @csrf <button class="rounded-full border border-red-200 px-6 py-3 font-semibold text-red-600 transition hover:bg-red-50">Abandonner</button>
        </form>
      @elseif (! $mine && $sos->status === 'searching')
        @if ($sos->directionsUrl())
          <a href="{{ $sos->directionsUrl() }}" target="_blank" rel="noopener" class="btn-accent">Voir l'itinéraire</a>
        @endif
        <form method="POST" action="{{ route('depanneur.requests.accept', $sos) }}">
          @csrf <button class="btn-primary">Accepter</button>
        </form>
        <form method="POST" action="{{ route('depanneur.requests.decline', $sos) }}">
          @csrf <button class="rounded-full border border-brand-700/20 px-6 py-3 font-semibold text-brand-700 transition hover:bg-brand-200/60">Refuser</button>
        </form>
      @endif
    </div>
  </section>

  <section class="card overflow-hidden !p-0">
    @if ($sos->embedUrl())
      <iframe src="{{ $sos->embedUrl() }}" class="h-80 w-full border-0 lg:h-full lg:min-h-[22rem]" loading="lazy" title="Position du client"></iframe>
    @else
      <div class="flex h-full min-h-[14rem] items-center justify-center p-6 text-center text-sm text-brand-900/60">
        Position GPS non transmise : fiez-vous au repère écrit et appelez le client après acceptation.
      </div>
    @endif
  </section>
</div>
@endsection