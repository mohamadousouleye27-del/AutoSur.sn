@extends('layouts.app')
@section('title', 'Mes véhicules')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-brand-700">Mes véhicules</h1>
    <p class="mt-1 text-brand-900/70">{{ $vehicles->count() }} véhicule(s) enregistré(s)</p>
  </div>
  <a href="{{ route('client.vehicles.create') }}" class="btn-primary">+ Ajouter un véhicule</a>
</div>

@if ($vehicles->isEmpty())
  <div class="card mt-8 py-16 text-center">
    <p class="text-lg font-bold text-brand-900">Aucun véhicule pour le moment</p>
    <p class="mt-1 text-sm text-brand-900/60">Ajoutez votre premier véhicule pour suivre son assurance et sa visite technique.</p>
    <a href="{{ route('client.vehicles.create') }}" class="btn-primary mt-6">Ajouter mon premier véhicule</a>
  </div>
@else
  <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($vehicles as $v)
      @php
        $ins = $v->status('insurance_expires_at');
        $vis = $v->status('technical_visit_expires_at');
      @endphp
      <article class="card">

              @if ($v->photoUrl())
          <img src="{{ $v->photoUrl() }}" alt="{{ $v->brand }} {{ $v->model }}" loading="lazy"
               class="mb-4 h-40 w-full rounded-2xl object-cover">
        @else
          <div class="mb-4 flex h-40 w-full items-center justify-center rounded-2xl bg-brand-400/15 text-xs font-semibold text-brand-700/60">
            Aucune photo
          </div>
        @endif
        
        <div class="flex items-start justify-between gap-3">
          <div>
            <h2 class="text-xl font-extrabold text-brand-900">{{ $v->brand }} {{ $v->model }}</h2>
            <p class="text-sm text-brand-900/60">{{ $v->year ?? '—' }} · {{ $v->color ?? 'Couleur non précisée' }}</p>
          </div>
          <span class="rounded-lg border-2 border-brand-700 px-2.5 py-1 text-xs font-extrabold tracking-wider text-brand-700">{{ $v->plate }}</span>
        </div>

        <dl class="mt-5 space-y-3 text-sm">
          <div class="flex items-center justify-between">
            <dt class="text-brand-900/70">Assurance</dt>
            <dd class="text-right">
              <span class="rounded-full px-3 py-1 text-xs font-bold {{ $ins['class'] }}">{{ $ins['label'] }}</span>
              @if ($v->insurance_expires_at)<span class="mt-1 block text-xs text-brand-900/50">{{ $v->insurance_expires_at->format('d/m/Y') }}</span>@endif
            </dd>
          </div>
          <div class="flex items-center justify-between">
            <dt class="text-brand-900/70">Visite technique</dt>
            <dd class="text-right">
              <span class="rounded-full px-3 py-1 text-xs font-bold {{ $vis['class'] }}">{{ $vis['label'] }}</span>
              @if ($v->technical_visit_expires_at)<span class="mt-1 block text-xs text-brand-900/50">{{ $v->technical_visit_expires_at->format('d/m/Y') }}</span>@endif
            </dd>
          </div>
        </dl>

        <div class="mt-5 flex gap-2">
          <a href="{{ route('client.vehicles.edit', $v) }}" class="btn-accent flex-1 !py-2 text-sm">Modifier</a>
          <form method="POST" action="{{ route('client.vehicles.destroy', $v) }}" class="flex-1"
                onsubmit="return confirm('Supprimer ce véhicule ?')">
            @csrf @method('DELETE')
            <button class="w-full rounded-full border border-red-200 px-6 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Supprimer</button>
          </form>
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection