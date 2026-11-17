@extends('layouts.app')
@section('title', 'Mes offres')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Mes offres</h1>
<p class="mt-1 text-brand-900/70">Suivi des offres envoyées aux clients.</p>

@error('offer')
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $message }}</p>
@enderror

@if ($offers->isEmpty())
  <div class="card mt-8 py-16 text-center text-sm text-brand-900/60">Vous n'avez pas encore envoyé d'offre.</div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($offers as $o)
      @php
        $badge = match ($o->status) {
          'accepted' => ['Acceptée', 'bg-brand-200 text-brand-700'],
          'rejected' => ['Non retenue', 'bg-slate-100 text-slate-500'],
          default    => ['En attente', 'bg-amber-100 text-amber-800'],
        };
      @endphp
      <article class="card flex flex-wrap items-center justify-between gap-4">
        <div>
          <p class="text-lg font-extrabold text-brand-900">{{ $o->quote->vehicle->brand }} {{ $o->quote->vehicle->model }}</p>
          <p class="text-sm text-brand-900/60">{{ $o->quote->coverageLabel() }}</p>
          <p class="mt-1 font-bold text-brand-700">{{ $o->formattedPremium() }} <span class="text-xs font-medium text-brand-900/50">par an</span></p>
          @if ($o->valid_until)<p class="text-xs text-brand-900/50">Valable jusqu'au {{ $o->valid_until->format('d/m/Y') }}</p>@endif
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <span class="rounded-full px-3 py-1 text-xs font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
          <a href="{{ route('assurance.requests.show', $o->quote) }}" class="btn-accent !py-2 text-sm">Détails</a>
          @if ($o->status === 'pending')
            <form method="POST" action="{{ route('assurance.offers.destroy', $o) }}" onsubmit="return confirm('Retirer cette offre ?')">
              @csrf @method('DELETE')
              <button class="rounded-full border border-red-200 px-5 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Retirer</button>
            </form>
          @endif
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection