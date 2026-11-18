@extends('layouts.app')
@section('title', 'Administration')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Administration</h1>
<p class="mt-1 text-brand-900/70">Vue d'ensemble de la plateforme AutoSur.</p>

@if ($pendingPartners)
  <a href="{{ route('admin.partners.index') }}" class="card mt-6 flex flex-wrap items-center justify-between gap-3 !border-amber-300 transition hover:shadow-md">
    <p class="font-extrabold text-brand-900">{{ $pendingPartners }} partenaire(s) en attente de validation</p>
    <span class="btn-primary !py-2 text-sm">Examiner</span>
  </a>
@endif

<div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
  @foreach (\App\Models\User::ROLE_LABELS as $role => $label)
    <div class="card">
      <p class="text-sm font-semibold text-brand-900/60">{{ $label }}s</p>
      <p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $roles[$role] ?? 0 }}</p>
    </div>
  @endforeach
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Véhicules</p><p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $vehicles }}</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Documents</p><p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $documents }}</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Devis ouverts</p><p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $openQuotes }}</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">RDV en attente</p><p class="mt-2 text-4xl font-extrabold text-brand-700">{{ $pendingVisits }}</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">SOS en cours</p><p class="mt-2 text-4xl font-extrabold {{ $activeSos ? 'text-red-600' : 'text-brand-700' }}">{{ $activeSos }}</p></div>
</div>

<div class="mt-6 grid gap-5 sm:grid-cols-2">
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Commissions d'assurance (estimées)</p>
    <p class="mt-2 text-3xl font-extrabold text-brand-700">{{ number_format($commissions, 0, ',', ' ') }} FCFA</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Frais visites techniques (estimés)</p>
    <p class="mt-2 text-3xl font-extrabold text-brand-700">{{ number_format($fees, 0, ',', ' ') }} FCFA</p></div>
</div>

<section class="card mt-8">
  <h2 class="text-lg font-extrabold">Dernières inscriptions</h2>
  @foreach ($recent as $u)
    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-brand-700/10 pt-3 text-sm">
      <span class="font-bold">{{ $u->name }}</span>
      <span class="text-brand-900/60">{{ \App\Models\User::ROLE_LABELS[$u->role] ?? $u->role }} · {{ $u->created_at->locale('fr')->diffForHumans() }}</span>
    </div>
  @endforeach
</section>
@endsection