@extends('layouts.app')
@section('title', 'Visite technique')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-brand-700">Visite technique</h1>
    <p class="mt-1 text-brand-900/70">Prenez rendez-vous sans vous déplacer pour la réserver.</p>
  </div>
  <a href="{{ route('client.inspections.create') }}" class="btn-primary">+ Prendre rendez-vous</a>
</div>

<div class="mt-6 rounded-2xl bg-brand-400/15 p-4 text-sm text-brand-900/80">
  Frais de service de facilitation : <strong>1 500 à 3 000 FCFA</strong> selon le type de véhicule,
  payables en ligne avec Wave ou Orange Money.
</div>

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

@if ($appointments->isEmpty())
  <div class="card mt-8 py-16 text-center">
    <p class="text-lg font-bold text-brand-900">Aucun rendez-vous</p>
    <p class="mt-1 text-sm text-brand-900/60">Planifiez votre prochaine visite technique en quelques clics.</p>
    <a href="{{ route('client.inspections.create') }}" class="btn-primary mt-6">Prendre rendez-vous</a>
  </div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($appointments as $a)
      @php $b = $a->badge(); @endphp
      <article class="card flex flex-wrap items-center justify-between gap-4">
        <div>
          <p class="text-lg font-extrabold text-brand-900">{{ $a->vehicle->brand }} {{ $a->vehicle->model }}
            <span class="ml-2 rounded-lg border-2 border-brand-700 px-2 py-0.5 text-xs font-extrabold tracking-wider text-brand-700">{{ $a->vehicle->plate }}</span>
          </p>
          <p class="text-sm text-brand-900/70">{{ $a->scheduled_date->locale('fr')->translatedFormat('l d F Y') }} · {{ $a->slotLabel() }}</p>
          <p class="text-sm text-brand-900/60">{{ $a->city }} · {{ $a->categoryLabel() }}</p>
          <p class="mt-1 text-sm font-bold text-brand-700">Frais de service : {{ $a->formattedFee() }}</p>
           @if ($a->garage)<p class="text-sm text-brand-900/60">Garage : {{ $a->garage->name }}</p>@endif
        </div>

        <div class="flex flex-wrap items-center gap-3">
          @if ($a->isPaid())
            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Payé</span>
          @elseif ($a->canCancel())
            <form method="POST" action="{{ route('client.inspections.pay', $a) }}">
              @csrf
              <button class="btn-primary !py-2 text-sm">Payer {{ $a->formattedFee() }}</button>
            </form>
          @endif

          <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>

          @if ($a->canCancel() && ! $a->isPaid())
            <form method="POST" action="{{ route('client.inspections.destroy', $a) }}" onsubmit="return confirm('Annuler ce rendez-vous ?')">
              @csrf @method('DELETE')
              <button class="rounded-full border border-red-200 px-5 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Annuler</button>
            </form>
          @endif
        </div>

                @if ($a->status === 'completed' && $a->garage_id)
          <div class="w-full border-t border-brand-700/10 pt-4">
            @if ($a->rating)
              <p class="text-sm text-brand-900/70">Votre avis : <span class="font-bold text-amber-500">{{ $a->rating->stars() }}</span>@if ($a->rating->comment) · « {{ $a->rating->comment }} »@endif</p>
            @else
              @include('client.partials.rating-form', ['action' => route('client.ratings.inspection', $a), 'label' => 'Noter ce garage'])
            @endif
          </div>
        @endif
        
      </article>
    @endforeach
  </div>
@endif
@endsection