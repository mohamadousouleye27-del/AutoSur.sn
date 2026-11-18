@extends('layouts.app')
@section('title', 'Rendez-vous')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Rendez-vous de visite technique</h1>

<nav class="mt-6 flex flex-wrap gap-2">
  @foreach (['new' => 'Nouvelles demandes', 'mine' => 'Confirmés', 'done' => 'Effectués'] as $key => $label)
    <a href="{{ route('garage.appointments.index', ['tab' => $key]) }}"
       class="rounded-full px-5 py-2 text-sm font-semibold {{ $tab === $key ? 'bg-brand-200 text-brand-700' : 'bg-white text-brand-900/70 hover:bg-brand-200/60' }}">{{ $label }}</a>
  @endforeach
</nav>

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

@if ($appointments->isEmpty())
  <div class="card mt-8 py-16 text-center text-sm text-brand-900/60">Aucun rendez-vous dans cette liste.</div>
@else
  <div class="mt-6 space-y-4">
    @foreach ($appointments as $a)
      @php $mine = $a->garage_id === auth()->id(); @endphp
      <article class="card">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <p class="text-lg font-extrabold text-brand-900">{{ $a->vehicle->brand }} {{ $a->vehicle->model }}
              <span class="ml-2 rounded-lg border-2 border-brand-700 px-2 py-0.5 text-xs font-extrabold tracking-wider text-brand-700">{{ $a->vehicle->plate }}</span>
            </p>
            <p class="text-sm text-brand-900/70">{{ $a->scheduled_date->locale('fr')->translatedFormat('l d F Y') }} · {{ $a->slotLabel() }}</p>
            <p class="text-sm text-brand-900/60">{{ $a->city }} · {{ $a->categoryLabel() }}</p>
            @if ($a->notes)<p class="mt-1 text-sm text-brand-900/70">« {{ $a->notes }} »</p>@endif
          </div>
          <div class="text-right text-sm">
            @if ($mine)
              <p class="font-bold">{{ $a->user->name }}</p>
              <p class="font-semibold text-brand-700">{{ $a->user->phone }}</p>
            @else
              <p class="font-bold">{{ \Illuminate\Support\Str::before($a->user->name, ' ') }}</p>
              <p class="text-xs text-brand-900/50">Coordonnées après confirmation</p>
            @endif
            <p class="mt-1 text-xs text-brand-900/50">Frais de service : {{ $a->formattedFee() }}</p>
          </div>
        </div>

        @if ($tab === 'new')
          <form method="POST" action="{{ route('garage.appointments.confirm', $a) }}" class="mt-4">
            @csrf
            <button class="btn-primary !py-2 text-sm">Confirmer le rendez-vous</button>
          </form>
        @elseif ($tab === 'mine')
          <div class="mt-4 flex flex-wrap items-start gap-3">
            @if (! $a->scheduled_date->isFuture())
              <details class="rounded-2xl border border-brand-700/20 px-4 py-2">
                <summary class="cursor-pointer text-sm font-semibold text-brand-700">Marquer comme effectuée</summary>
                <form method="POST" action="{{ route('garage.appointments.complete', $a) }}" class="mt-3 flex flex-wrap items-end gap-3">
                  @csrf
                  <div>
                    <label class="mb-1 block text-xs font-semibold">Nouvelle date d'expiration *</label>
                    <input type="date" name="new_expiry" min="{{ today()->addDay()->format('Y-m-d') }}" required class="input !py-2 text-sm">
                  </div>
                  <button class="btn-primary !py-2 text-sm">Valider</button>
                </form>
              </details>
            @else
              <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500">Clôturable à partir de la date du rendez-vous</span>
            @endif

            <form method="POST" action="{{ route('garage.appointments.release', $a) }}" onsubmit="return confirm('Libérer ce rendez-vous ?')">
              @csrf
              <button class="rounded-full border border-red-200 px-5 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Libérer</button>
            </form>
          </div>
        @endif
      </article>
    @endforeach
  </div>
@endif
@endsection