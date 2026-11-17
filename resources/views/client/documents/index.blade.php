@extends('layouts.app')
@section('title', 'Coffre-fort')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-brand-700">Coffre-fort numérique</h1>
    <p class="mt-1 text-brand-900/70">Vos documents automobiles, classés et protégés.</p>
  </div>
  <a href="{{ route('client.documents.create') }}" class="btn-primary">+ Ajouter un document</a>
</div>

{{-- Filtres --}}
<form method="GET" class="mt-6 flex flex-wrap gap-3">
  <select name="type" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm">
    <option value="">Tous les types</option>
    @foreach (\App\Models\Document::TYPES as $key => $label)
      <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
    @endforeach
  </select>
  <select name="vehicle" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm">
    <option value="">Tous les véhicules</option>
    @foreach ($vehicles as $v)
      <option value="{{ $v->id }}" @selected((string) request('vehicle') === (string) $v->id)>{{ $v->plate }} · {{ $v->brand }} {{ $v->model }}</option>
    @endforeach
  </select>
  @if (request('type') || request('vehicle'))
    <a href="{{ route('client.documents.index') }}" class="self-center text-sm font-semibold text-brand-700 underline">Réinitialiser</a>
  @endif
</form>

@if ($documents->isEmpty())
  <div class="card mt-8 py-16 text-center">
    <p class="text-lg font-bold text-brand-900">Aucun document</p>
    <p class="mt-1 text-sm text-brand-900/60">Ajoutez votre carte grise, votre attestation d'assurance, vos factures…</p>
    <a href="{{ route('client.documents.create') }}" class="btn-primary mt-6">Ajouter un document</a>
  </div>
@else
  <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($documents as $doc)
      @php $st = $doc->status(); @endphp
      <article class="card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-200 text-xs font-extrabold text-brand-700">
            {{ $doc->isImage() ? 'IMG' : 'PDF' }}
          </div>
          <span class="rounded-full px-3 py-1 text-xs font-bold {{ $st['class'] }}">{{ $st['label'] }}</span>
        </div>

        <h2 class="mt-4 truncate text-lg font-extrabold text-brand-900" title="{{ $doc->title }}">{{ $doc->title }}</h2>
        <p class="text-sm text-brand-900/60">{{ $doc->typeLabel() }} · {{ $doc->humanSize() }}</p>

        <dl class="mt-3 space-y-1 text-sm text-brand-900/70">
          <div class="flex justify-between"><dt>Véhicule</dt><dd class="font-semibold">{{ $doc->vehicle?->plate ?? '—' }}</dd></div>
          <div class="flex justify-between"><dt>Expiration</dt><dd class="font-semibold">{{ $doc->expires_at?->format('d/m/Y') ?? '—' }}</dd></div>
        </dl>

        <div class="mt-5 grid grid-cols-2 gap-2">
          <a href="{{ route('client.documents.view', $doc) }}" target="_blank" class="btn-accent !py-2 text-sm">Voir</a>
          <a href="{{ route('client.documents.download', $doc) }}" class="btn-primary !py-2 text-sm">Télécharger</a>
          <a href="{{ route('client.documents.edit', $doc) }}" class="rounded-full border border-brand-700/20 py-2 text-center text-sm font-semibold text-brand-700 transition hover:bg-brand-200/60">Modifier</a>
          <form method="POST" action="{{ route('client.documents.destroy', $doc) }}" onsubmit="return confirm('Supprimer ce document ?')">
            @csrf @method('DELETE')
            <button class="w-full rounded-full border border-red-200 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Supprimer</button>
          </form>
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection