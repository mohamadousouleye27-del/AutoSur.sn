@extends('layouts.app')
@section('title', 'Demande de devis')

@section('content')
@php
  $won = $offer && $offer->status === 'accepted';
  $locked = $offer && $offer->status !== 'pending';
@endphp

<a href="{{ route('assurance.requests.index') }}" class="text-sm font-semibold text-brand-700 underline">← Demandes</a>
<h1 class="mt-3 text-3xl font-extrabold text-brand-700">{{ $quote->vehicle->brand }} {{ $quote->vehicle->model }}</h1>

<div class="mt-6 grid gap-6 lg:grid-cols-2">

  <section class="card">
    <h2 class="text-lg font-extrabold">Demande</h2>
    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
      <div><dt class="text-brand-900/60">Marque / modèle</dt><dd class="font-bold">{{ $quote->vehicle->brand }} {{ $quote->vehicle->model }}</dd></div>
      <div><dt class="text-brand-900/60">Année</dt><dd class="font-bold">{{ $quote->vehicle->year ?? '—' }}</dd></div>
      <div><dt class="text-brand-900/60">Couleur</dt><dd class="font-bold">{{ $quote->vehicle->color ?? '—' }}</dd></div>
      <div><dt class="text-brand-900/60">Formule</dt><dd class="font-bold">{{ $quote->coverageLabel() }}</dd></div>
      <div><dt class="text-brand-900/60">Usage</dt><dd class="font-bold">{{ $quote->usageLabel() }}</dd></div>
      <div><dt class="text-brand-900/60">Début souhaité</dt><dd class="font-bold">{{ $quote->start_date?->format('d/m/Y') ?? 'Dès que possible' }}</dd></div>
      @if ($quote->notes)
        <div class="sm:col-span-2"><dt class="text-brand-900/60">Informations du client</dt><dd>{{ $quote->notes }}</dd></div>
      @endif
    </dl>

    <div class="mt-5 rounded-2xl bg-brand-400/15 p-4 text-sm">
      <p class="font-bold">Client</p>
      @if ($won)
        <p>{{ $quote->user->name }}</p>
        <p class="font-semibold text-brand-700">{{ $quote->user->phone }}</p>
        <p class="mt-1 text-xs text-brand-900/60">Offre acceptée : contactez le client pour finaliser la souscription.</p>
      @else
        <p>{{ \Illuminate\Support\Str::before($quote->user->name, ' ') }}</p>
        <p class="text-xs text-brand-900/60">Les coordonnées complètes sont communiquées si votre offre est acceptée.</p>
      @endif
    </div>
  </section>

  <section class="card">
    <h2 class="text-lg font-extrabold">{{ $offer ? 'Votre offre' : 'Envoyer une offre' }}</h2>

    @if ($won)
      <p class="mt-3 rounded-2xl bg-brand-200 px-4 py-3 text-sm font-semibold text-brand-700">Offre acceptée par le client 🎉</p>
    @elseif ($offer && $offer->status === 'rejected')
      <p class="mt-3 rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-600">Offre non retenue.</p>
    @endif

    <form method="POST" action="{{ route('assurance.requests.offer', $quote) }}" class="mt-4 space-y-4">
      @csrf
      <div>
        <label class="mb-1 block text-sm font-semibold">Prime annuelle (FCFA) *</label>
        <input type="number" name="annual_premium" min="10000" step="500" value="{{ old('annual_premium', $offer?->annual_premium) }}" class="input" required @disabled($locked)>
        @error('annual_premium') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="mb-1 block text-sm font-semibold">Offre valable jusqu'au *</label>
        <input type="date" name="valid_until" min="{{ today()->addDay()->format('Y-m-d') }}"
               value="{{ old('valid_until', $offer?->valid_until?->format('Y-m-d') ?? today()->addDays(15)->format('Y-m-d')) }}" class="input" required @disabled($locked)>
        @error('valid_until') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="mb-1 block text-sm font-semibold">Garanties et conditions</label>
        <textarea name="details" rows="4" maxlength="600" placeholder="Ex : responsabilité civile, défense et recours, assistance 24h/24…" @disabled($locked)
                  class="w-full rounded-2xl border border-brand-700/20 bg-white px-5 py-3 outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-400/40">{{ old('details', $offer?->details) }}</textarea>
        @error('details') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <p class="text-xs text-brand-900/60">Commission d'apport d'affaires applicable : <strong>{{ rtrim(rtrim(number_format($offer?->commission_rate ?? $rate, 2), '0'), '.') }} %</strong></p>

      @unless ($locked)
        <button class="btn-primary w-full">{{ $offer ? "Mettre à jour l'offre" : "Envoyer l'offre" }}</button>
      @endunless
    </form>
  </section>
</div>
@endsection