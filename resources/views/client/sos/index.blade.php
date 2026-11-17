@extends('layouts.app')
@section('title', 'SOS Panne')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">SOS Panne</h1>
<p class="mt-1 text-brand-900/70">Un dépanneur proche de vous, localisé en temps réel.</p>

@if ($active)
  @php $b = $active->badge(); @endphp
  <a href="{{ route('client.sos.show', $active) }}" class="card mt-6 flex flex-wrap items-center justify-between gap-3 !border-amber-300 transition hover:shadow-md">
    <div>
      <p class="font-extrabold text-brand-900">Une demande est en cours</p>
      <p class="text-sm text-brand-900/60">{{ $active->problemLabel() }} · {{ $active->created_at->locale('fr')->diffForHumans() }}</p>
    </div>
    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
  </a>
@else
  <div class="card mt-6 max-w-xl text-center !p-10">
    <p class="text-lg font-bold text-brand-900">En panne ou en difficulté ?</p>
    <p class="mt-1 text-sm text-brand-900/60">Envoyez votre position en quelques secondes.</p>
    <a href="{{ route('client.sos.create') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-red-600 px-10 py-4 text-lg font-extrabold text-white shadow-lg shadow-red-600/30 transition hover:bg-red-700 active:scale-95">Demander un dépanneur</a>
  </div>
@endif

<h2 class="mt-10 text-xl font-extrabold text-brand-900">Historique</h2>
@if ($history->isEmpty())
  <p class="mt-3 text-sm text-brand-900/60">Aucune demande pour le moment.</p>
@else
  <div class="mt-4 space-y-3">
    @foreach ($history as $r)
      @php $b = $r->badge(); @endphp
      <a href="{{ route('client.sos.show', $r) }}" class="card flex flex-wrap items-center justify-between gap-3 transition hover:shadow-md">
        <div>
          <p class="font-bold text-brand-900">{{ $r->problemLabel() }}@if ($r->vehicle) · {{ $r->vehicle->brand }} {{ $r->vehicle->model }}@endif</p>
          <p class="text-xs text-brand-900/50">{{ $r->created_at->locale('fr')->translatedFormat('d F Y à H:i') }}</p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
      </a>
    @endforeach
  </div>
@endif
@endsection