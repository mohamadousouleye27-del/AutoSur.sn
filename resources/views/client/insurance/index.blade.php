@extends('layouts.app')
@section('title', 'Assurance')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-brand-700">Assurance</h1>
    <p class="mt-1 text-brand-900/70">Demandez un devis et comparez les offres de nos partenaires.</p>
  </div>
  <a href="{{ route('client.insurance.create') }}" class="btn-primary">+ Demander un devis</a>
</div>

@if ($quotes->isEmpty())
  <div class="card mt-8 py-16 text-center">
    <p class="text-lg font-bold text-brand-900">Aucune demande de devis</p>
    <p class="mt-1 text-sm text-brand-900/60">Recevez plusieurs offres pour votre véhicule, sans vous déplacer.</p>
    <a href="{{ route('client.insurance.create') }}" class="btn-primary mt-6">Demander mon premier devis</a>
  </div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($quotes as $q)
      @php $b = $q->badge(); @endphp
      <a href="{{ route('client.insurance.show', $q) }}" class="card flex flex-wrap items-center justify-between gap-4 transition hover:shadow-md">
        <div>
          <p class="text-lg font-extrabold text-brand-900">{{ $q->vehicle->brand }} {{ $q->vehicle->model }}
            <span class="ml-2 rounded-lg border-2 border-brand-700 px-2 py-0.5 text-xs font-extrabold tracking-wider text-brand-700">{{ $q->vehicle->plate }}</span>
          </p>
          <p class="text-sm text-brand-900/60">{{ $q->coverageLabel() }} · {{ $q->usageLabel() }}</p>
          <p class="text-xs text-brand-900/40">Demandé {{ $q->created_at->locale('fr')->diffForHumans() }}</p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
      </a>
    @endforeach
  </div>
@endif
@endsection