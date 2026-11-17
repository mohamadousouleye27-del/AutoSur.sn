@extends('layouts.app')
@section('title', $vehicle->exists ? 'Modifier le véhicule' : 'Ajouter un véhicule')

@section('content')
<a href="{{ route('client.vehicles.index') }}" class="text-sm font-semibold text-brand-700 underline">← Retour</a>
<h1 class="mt-3 text-3xl font-extrabold text-brand-700">{{ $vehicle->exists ? 'Modifier le véhicule' : 'Ajouter un véhicule' }}</h1>

@php
  $fields = [
    ['plate', 'Immatriculation *', 'text', 'DK-1234-AB'],
    ['brand', 'Marque *', 'text', 'Toyota'],
    ['model', 'Modèle *', 'text', 'Corolla'],
    ['year', 'Année', 'number', '2018'],
    ['color', 'Couleur', 'text', 'Gris'],
    ['chassis_number', 'Numéro de châssis', 'text', 'VIN / numéro de série'],
  ];
@endphp

<form method="POST"
      enctype="multipart/form-data"
      action="{{ $vehicle->exists ? route('client.vehicles.update', $vehicle) : route('client.vehicles.store') }}"
      class="card mt-6 max-w-3xl !p-8">
  @csrf
  @if ($vehicle->exists) @method('PUT') @endif

    <div class="mb-6">
    <label class="mb-2 block text-sm font-semibold text-brand-900">Photo du véhicule (facultative)</label>

    <div class="flex flex-wrap items-center gap-5">
      <div class="h-28 w-44 shrink-0 overflow-hidden rounded-2xl bg-brand-400/15">
        <img id="photo-preview"
             src="{{ $vehicle->photoUrl() }}"
             alt="Aperçu"
             class="h-full w-full object-cover {{ $vehicle->photoUrl() ? '' : 'hidden' }}">
        <div id="photo-empty" class="flex h-full items-center justify-center text-xs font-semibold text-brand-700/60 {{ $vehicle->photoUrl() ? 'hidden' : '' }}">
          Aucune photo
        </div>
      </div>

      <div>
        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm file:mr-3 file:rounded-full file:border-0 file:bg-brand-200 file:px-5 file:py-2 file:font-semibold file:text-brand-700 hover:file:brightness-95">
        <p class="mt-1 text-xs text-brand-900/50">JPG, PNG ou WebP · 2 Mo maximum</p>
        @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

        @if ($vehicle->photo_path)
          <label class="mt-3 flex items-center gap-2 text-sm text-red-600">
            <input type="checkbox" name="remove_photo" value="1" class="rounded border-brand-700/30"> Supprimer la photo actuelle
          </label>
        @endif
      </div>
    </div>
  </div>

  <div class="grid gap-5 sm:grid-cols-2">
    @foreach ($fields as [$name, $label, $type, $placeholder])
      <div>
        <label class="mb-1 block text-sm font-semibold text-brand-900">{{ $label }}</label>
        <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $vehicle->$name) }}" placeholder="{{ $placeholder }}" class="input">
        @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    @endforeach

    <div>
      <label class="mb-1 block text-sm font-semibold text-brand-900">Fin de validité de l'assurance</label>
      <input type="date" name="insurance_expires_at" value="{{ old('insurance_expires_at', $vehicle->insurance_expires_at?->format('Y-m-d')) }}" class="input">
      @error('insurance_expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="mb-1 block text-sm font-semibold text-brand-900">Fin de validité de la visite technique</label>
      <input type="date" name="technical_visit_expires_at" value="{{ old('technical_visit_expires_at', $vehicle->technical_visit_expires_at?->format('Y-m-d')) }}" class="input">
      @error('technical_visit_expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
  </div>

  <div class="mt-8 flex gap-3">
    <button class="btn-primary">{{ $vehicle->exists ? 'Enregistrer' : 'Ajouter le véhicule' }}</button>
    <a href="{{ route('client.vehicles.index') }}" class="btn-accent">Annuler</a>
  </div>
</form>

<script>
  document.getElementById('photo').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const img = document.getElementById('photo-preview');
    img.src = URL.createObjectURL(file);
    img.classList.remove('hidden');
    document.getElementById('photo-empty').classList.add('hidden');
  });
</script>
@endsection