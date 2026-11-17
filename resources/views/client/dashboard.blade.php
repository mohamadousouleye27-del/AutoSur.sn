@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Bonjour, {{ auth()->user()->name }} 👋</h1>
<p class="mt-1 text-brand-900/70">Voici l'état de vos véhicules.</p>

<div class="mt-8 grid gap-5 sm:grid-cols-3">
  <div class="card">
    <p class="text-sm font-semibold text-brand-900/60">Véhicules</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $vehicles->count() }}</p>
  </div>
  <div class="card">
    <p class="text-sm font-semibold text-brand-900/60">Échéances à surveiller</p>
    <p class="mt-2 text-4xl font-extrabold {{ $alerts->count() ? 'text-amber-600' : 'text-brand-700' }}">{{ $alerts->count() }}</p>
  </div>
    <div class="card">
    <p class="text-sm font-semibold text-brand-900/60">Documents</p>
    <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $documentsCount }}</p>
    <a href="{{ route('client.documents.index') }}" class="text-xs font-semibold text-brand-700 underline">Ouvrir le coffre-fort</a>
  </div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-3">
  <section class="card lg:col-span-2">
    <h2 class="text-lg font-extrabold text-brand-900">Échéances à surveiller</h2>

    @forelse ($alerts as $alert)
      <div class="mt-4 flex items-center justify-between gap-3 rounded-2xl border border-brand-700/10 p-4">
        <div>
          <p class="font-bold text-brand-900">{{ $alert['title'] }}</p>
          <p class="text-sm text-brand-900/60">{{ $alert['subtitle'] }} · {{ $alert['date']->format('d/m/Y') }}</p>
        </div>
        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $alert['status']['class'] }}">{{ $alert['status']['label'] }}</span>
      </div>
    @empty
      <p class="mt-4 text-sm text-brand-900/60">
        {{ $vehicles->isEmpty() ? 'Ajoutez un véhicule pour suivre ses échéances.' : 'Aucune échéance proche : tout est en règle ✅' }}
      </p>
    @endforelse
  </section>

  <section class="card">
    <h2 class="text-lg font-extrabold text-brand-900">Actions rapides</h2>
    <div class="mt-4 space-y-3">
      <a href="{{ route('client.vehicles.create') }}" class="btn-primary w-full">Ajouter un véhicule</a>
      <a href="{{ route('client.vehicles.index') }}" class="btn-accent w-full">Voir mes véhicules</a>
      <a href="{{ route('client.documents.create') }}" class="btn-accent w-full">Ajouter un document</a>
      <a href="{{ route('client.inspections.create') }}" class="btn-accent w-full">Prendre RDV visite technique</a>
      <a href="{{ route('client.sos.create') }}" class="inline-flex w-full items-center justify-center rounded-full bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700">SOS Panne</a>
    </div>
  </section>
</div>
@endsection