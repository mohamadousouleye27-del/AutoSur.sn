@extends('layouts.app')
@section('title', 'Espace garage')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Bonjour, {{ auth()->user()->name }}</h1>
<p class="mt-1 text-brand-900/70">Vos rendez-vous de visite technique.</p>

<div class="mt-8 grid gap-5 sm:grid-cols-3">
  <a href="{{ route('garage.appointments.index') }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Nouvelles demandes</p>
    <p class="mt-2 text-4xl font-extrabold {{ $newCount ? 'text-amber-600' : 'text-brand-700' }}">{{ $newCount }}</p>
  </a>
  <a href="{{ route('garage.appointments.index', ['tab' => 'mine']) }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Rendez-vous confirmés</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $confirmed->count() }}</p>
  </a>
  <a href="{{ route('garage.appointments.index', ['tab' => 'done']) }}" class="card transition hover:shadow-md">
    <p class="text-sm font-semibold text-brand-900/60">Visites effectuées</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $doneCount }}</p>
  </a>
</div>

<section class="card mt-8">
  <h2 class="text-lg font-extrabold">Prochains rendez-vous</h2>
  @forelse ($confirmed->take(5) as $a)
    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 rounded-2xl border border-brand-700/10 p-4">
      <div>
        <p class="font-bold text-brand-900">{{ $a->vehicle->brand }} {{ $a->vehicle->model }} · {{ $a->vehicle->plate }}</p>
        <p class="text-sm text-brand-900/60">{{ $a->user->name }} · {{ $a->user->phone }}</p>
      </div>
      <p class="text-sm font-semibold text-brand-700">{{ $a->scheduled_date->locale('fr')->translatedFormat('D d F') }} · {{ $a->slotLabel() }}</p>
    </div>
  @empty
    <p class="mt-3 text-sm text-brand-900/60">Aucun rendez-vous confirmé pour le moment.</p>
  @endforelse
</section>
@endsection