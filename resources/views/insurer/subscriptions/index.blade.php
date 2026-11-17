@extends('layouts.app')
@section('title', 'Souscriptions')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Souscriptions</h1>
<p class="mt-1 text-brand-900/70">Offres acceptées et commissions d'apport d'affaires.</p>

<div class="mt-8 grid gap-5 sm:grid-cols-3">
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Souscriptions</p><p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $offers->count() }}</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Primes annuelles</p><p class="mt-2 text-2xl font-extrabold text-brand-700">{{ number_format($totalPremium, 0, ',', ' ') }} FCFA</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Commissions estimées</p><p class="mt-2 text-2xl font-extrabold text-brand-700">{{ number_format($totalCommission, 0, ',', ' ') }} FCFA</p></div>
</div>

@if ($offers->isEmpty())
  <div class="card mt-8 py-16 text-center text-sm text-brand-900/60">Aucune offre acceptée pour le moment.</div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($offers as $o)
      <article class="card flex flex-wrap items-center justify-between gap-4">
        <div>
          <p class="text-lg font-extrabold text-brand-900">{{ $o->quote->vehicle->brand }} {{ $o->quote->vehicle->model }} · {{ $o->quote->vehicle->plate }}</p>
          <p class="text-sm text-brand-900/70">{{ $o->quote->user->name }} · <span class="font-semibold text-brand-700">{{ $o->quote->user->phone }}</span></p>
          <p class="text-xs text-brand-900/50">{{ $o->quote->coverageLabel() }} · acceptée {{ $o->updated_at->locale('fr')->diffForHumans() }}</p>
        </div>
        <div class="text-right">
          <p class="font-bold text-brand-700">{{ $o->formattedPremium() }}</p>
          <p class="text-xs text-brand-900/60">Commission {{ rtrim(rtrim(number_format($o->commission_rate, 2), '0'), '.') }} % : <strong>{{ $o->formattedCommission() }}</strong></p>
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection