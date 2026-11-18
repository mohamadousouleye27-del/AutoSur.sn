@extends('layouts.app')
@section('title', 'Mes interventions')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Mes interventions</h1>

@if ($jobs->isEmpty())
  <div class="card mt-8 py-16 text-center text-sm text-brand-900/60">Aucune intervention pour le moment.</div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($jobs as $j)
      @php $b = $j->badge(); @endphp
      <a href="{{ route('depanneur.requests.show', $j) }}" class="card flex flex-wrap items-center justify-between gap-3 transition hover:shadow-md">
        <div>
          <p class="font-extrabold text-brand-900">{{ $j->problemLabel() }}@if ($j->vehicle) · {{ $j->vehicle->brand }} {{ $j->vehicle->model }}@endif</p>
          <p class="text-sm text-brand-900/60">{{ $j->user->name }} · {{ $j->user->phone }}</p>
          <p class="text-xs text-brand-900/40">{{ $j->updated_at->locale('fr')->translatedFormat('d F Y à H:i') }}</p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
      </a>
    @endforeach
  </div>
@endif
@endsection