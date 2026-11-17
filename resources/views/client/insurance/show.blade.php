@extends('layouts.app')
@section('title', 'Demande de devis')

@section('content')
@php $b = $quote->badge(); @endphp

<a href="{{ route('client.insurance.index') }}" class="text-sm font-semibold text-brand-700 underline">← Mes demandes</a>

<div class="mt-3 flex flex-wrap items-center justify-between gap-3">
  <h1 class="text-3xl font-extrabold text-brand-700">{{ $quote->vehicle->brand }} {{ $quote->vehicle->model }}</h1>
  <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
</div>

<div class="card mt-5 max-w-3xl">
  <dl class="grid gap-3 text-sm sm:grid-cols-2">
    <div><dt class="text-brand-900/60">Immatriculation</dt><dd class="font-bold">{{ $quote->vehicle->plate }}</dd></div>
    <div><dt class="text-brand-900/60">Formule</dt><dd class="font-bold">{{ $quote->coverageLabel() }}</dd></div>
    <div><dt class="text-brand-900/60">Usage</dt><dd class="font-bold">{{ $quote->usageLabel() }}</dd></div>
    <div><dt class="text-brand-900/60">Début souhaité</dt><dd class="font-bold">{{ $quote->start_date?->format('d/m/Y') ?? 'Dès que possible' }}</dd></div>
    @if ($quote->notes)
      <div class="sm:col-span-2"><dt class="text-brand-900/60">Informations</dt><dd>{{ $quote->notes }}</dd></div>
    @endif
  </dl>

  @if ($quote->status === 'open')
    <form method="POST" action="{{ route('client.insurance.destroy', $quote) }}" class="mt-5"
          onsubmit="return confirm('Annuler cette demande ?')">
      @csrf @method('DELETE')
      <button class="rounded-full border border-red-200 px-6 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Annuler la demande</button>
    </form>
  @endif
</div>

<h2 class="mt-10 text-xl font-extrabold text-brand-900">Offres reçues</h2>

@error('offer')
  <p class="mt-3 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $message }}</p>
@enderror

@if ($offers->isEmpty())
  <div class="card mt-4 max-w-3xl py-10 text-center text-sm text-brand-900/60">
    Aucune offre pour le moment. Les assureurs partenaires étudient votre demande.
  </div>
@else
  <div class="mt-4 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($offers as $offer)
      @php $expired = $offer->isExpired(); @endphp
      <article class="card {{ $offer->status === 'accepted' ? '!border-brand-700 ring-2 ring-brand-200' : '' }} {{ $offer->status === 'rejected' ? 'opacity-60' : '' }}">
        <p class="text-sm font-semibold text-brand-900/60">{{ $offer->insurer->name }}</p>
        <p class="mt-1 text-3xl font-extrabold text-brand-700">{{ $offer->formattedPremium() }}</p>
        <p class="text-xs text-brand-900/50">par an</p>

        @if ($offer->details)
          <p class="mt-3 text-sm text-brand-900/70">{{ $offer->details }}</p>
        @endif

        <p class="mt-3 text-xs text-brand-900/50">
          @if ($offer->valid_until) {{ $expired ? 'Offre expirée le' : "Valable jusqu'au" }} {{ $offer->valid_until->format('d/m/Y') }} @endif
        </p>

        <div class="mt-4">
          @if ($offer->status === 'accepted')
            <span class="badge">Offre acceptée</span>
          @elseif ($offer->status === 'rejected')
            <span class="text-xs font-semibold text-brand-900/50">Non retenue</span>
          @elseif ($quote->status === 'open' && ! $expired)
            <form method="POST" action="{{ route('client.insurance.accept', $offer) }}"
                  onsubmit="return confirm('Accepter cette offre ?')">
              @csrf
              <button class="btn-primary w-full !py-2 text-sm">Accepter cette offre</button>
            </form>
          @endif
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection