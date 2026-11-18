@extends('layouts.app')
@section('title', 'Demandes SOS')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Demandes SOS</h1>

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

@if ($active)
  <a href="{{ route('depanneur.requests.show', $active) }}" class="card mt-6 flex flex-wrap items-center justify-between gap-3 !border-amber-300 transition hover:shadow-md">
    <div>
      <p class="font-extrabold">Vous avez une intervention en cours</p>
      <p class="text-sm text-brand-900/60">{{ $active->problemLabel() }} · client : {{ $active->user->name }}</p>
    </div>
    <span class="btn-primary !py-2 text-sm">Ouvrir</span>
  </a>

@elseif (! $available)
  <div class="card mt-6 py-14 text-center">
    <p class="font-bold text-brand-900">Vous êtes indisponible</p>
    <p class="mt-1 text-sm text-brand-900/60">Passez en « disponible » depuis le tableau de bord pour recevoir les demandes.</p>
    <a href="{{ route('depanneur.dashboard') }}" class="btn-primary mt-5">Aller au tableau de bord</a>
  </div>

@elseif ($requests->isEmpty())
  <div class="card mt-6 py-14 text-center text-sm text-brand-900/60">
    Aucune demande pour le moment. Cette page se met à jour automatiquement.
  </div>

@else
  <div class="mt-6 space-y-4">
    @foreach ($requests as $r)
      <article class="card flex flex-wrap items-center justify-between gap-4 !border-red-200">
        <div>
          <p class="text-lg font-extrabold text-brand-900">{{ $r->problemLabel() }}</p>
          <p class="text-sm text-brand-900/70">{{ $r->vehicle ? $r->vehicle->brand . ' ' . $r->vehicle->model : 'Véhicule non précisé' }}</p>
          @if ($r->landmark)<p class="text-sm text-brand-900/60">{{ $r->landmark }}</p>@endif
          <p class="text-xs text-brand-900/40">Reçue {{ $r->created_at->locale('fr')->diffForHumans() }}</p>
        </div>
        <a href="{{ route('depanneur.requests.show', $r) }}" class="btn-primary !py-2 text-sm">Voir la demande</a>
      </article>
    @endforeach
  </div>
@endif

@if ($available && ! $active)
<script>
  const shown = {{ $requests->count() }};
  setInterval(async () => {
    try {
      const res = await fetch(@json(route('depanneur.requests.count')), { headers: { 'Accept': 'application/json' } });
      if (!res.ok) return;
      const data = await res.json();
      if (data.count !== shown) location.reload();
    } catch (e) {}
  }, 8000);
</script>
@endif
@endsection