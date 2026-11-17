@extends('layouts.app')
@section('title', $document->exists ? 'Modifier le document' : 'Ajouter un document')

@section('content')
<a href="{{ route('client.documents.index') }}" class="text-sm font-semibold text-brand-700 underline">← Retour</a>
<h1 class="mt-3 text-3xl font-extrabold text-brand-700">{{ $document->exists ? 'Modifier le document' : 'Ajouter un document' }}</h1>

<form method="POST" enctype="multipart/form-data"
      action="{{ $document->exists ? route('client.documents.update', $document) : route('client.documents.store') }}"
      class="card mt-6 max-w-3xl !p-8">
  @csrf
  @if ($document->exists) @method('PUT') @endif

  <div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
      <label class="mb-1 block text-sm font-semibold">Titre *</label>
      <input type="text" name="title" value="{{ old('title', $document->title) }}" placeholder="Ex : Carte grise Toyota Corolla" class="input" required>
      @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="mb-1 block text-sm font-semibold">Type *</label>
      <select name="type" class="input" required>
        @foreach (\App\Models\Document::TYPES as $key => $label)
          <option value="{{ $key }}" @selected(old('type', $document->type) === $key)>{{ $label }}</option>
        @endforeach
      </select>
      @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="mb-1 block text-sm font-semibold">Véhicule concerné</label>
      <select name="vehicle_id" class="input">
        <option value="">Aucun (document personnel)</option>
        @foreach ($vehicles as $v)
          <option value="{{ $v->id }}" @selected((string) old('vehicle_id', $document->vehicle_id) === (string) $v->id)>{{ $v->plate }} · {{ $v->brand }} {{ $v->model }}</option>
        @endforeach
      </select>
      @error('vehicle_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
      <label class="mb-1 block text-sm font-semibold">Date d'expiration (si le document en a une)</label>
      <input type="date" name="expires_at" value="{{ old('expires_at', $document->expires_at?->format('Y-m-d')) }}" class="input sm:max-w-xs">
      @error('expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @unless ($document->exists)
      <div class="sm:col-span-2">
        <label class="mb-1 block text-sm font-semibold">Fichier * (PDF, JPG ou PNG · 5 Mo max)</label>
        <input type="file" name="file" accept=".pdf,image/jpeg,image/png" required
               class="block w-full text-sm file:mr-3 file:rounded-full file:border-0 file:bg-brand-200 file:px-5 file:py-2 file:font-semibold file:text-brand-700 hover:file:brightness-95">
        @error('file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    @else
      <p class="text-sm text-brand-900/60 sm:col-span-2">Fichier actuel : <strong>{{ $document->original_name }}</strong> (le fichier ne peut pas être remplacé : supprimez ce document et ajoutez-en un nouveau).</p>
    @endunless
  </div>

  <div class="mt-8 flex gap-3">
    <button class="btn-primary">{{ $document->exists ? 'Enregistrer' : 'Envoyer le document' }}</button>
    <a href="{{ route('client.documents.index') }}" class="btn-accent">Annuler</a>
  </div>
</form>
@endsection