@extends('layouts.app')
@section('title', 'Demander un devis')

@section('content')
<a href="{{ route('client.insurance.index') }}" class="text-sm font-semibold text-brand-700 underline">← Retour</a>
<h1 class="mt-3 text-3xl font-extrabold text-brand-700">Demander un devis</h1>

@if ($vehicles->isEmpty())
  <div class="card mt-6 max-w-3xl py-12 text-center">
    <p class="font-bold text-brand-900">Ajoutez d'abord un véhicule</p>
    <p class="mt-1 text-sm text-brand-900/60">Un devis est établi pour un véhicule précis.</p>
    <a href="{{ route('client.vehicles.create') }}" class="btn-primary mt-6">Ajouter un véhicule</a>
  </div>
@else
  <form method="POST" action="{{ route('client.insurance.store') }}" class="card mt-6 max-w-3xl !p-8">
    @csrf

    <div>
      <label class="mb-1 block text-sm font-semibold">Véhicule *</label>
      <select name="vehicle_id" class="input" required>
        <option value="">Choisir un véhicule</option>
        @foreach ($vehicles as $v)
          <option value="{{ $v->id }}" @selected((string) old('vehicle_id', $selected) === (string) $v->id)>{{ $v->plate }} · {{ $v->brand }} {{ $v->model }}</option>
        @endforeach
      </select>
      @error('vehicle_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6">
      <p class="mb-2 text-sm font-semibold">Formule souhaitée *</p>
      <div class="grid gap-2">
        @foreach (\App\Models\InsuranceQuote::COVERAGES as $key => $label)
          <label class="cursor-pointer">
            <input type="radio" name="coverage" value="{{ $key }}" class="peer sr-only" @checked(old('coverage', 'tiers_plus') === $key)>
            <span class="block rounded-2xl border border-brand-700/20 px-5 py-3 text-sm font-semibold peer-checked:border-brand-700 peer-checked:bg-brand-200">{{ $label }}</span>
          </label>
        @endforeach
      </div>
      @error('coverage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-semibold">Usage du véhicule *</label>
        <select name="usage" class="input" required>
          @foreach (\App\Models\InsuranceQuote::USAGES as $key => $label)
            <option value="{{ $key }}" @selected(old('usage', 'personnel') === $key)>{{ $label }}</option>
          @endforeach
        </select>
        @error('usage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="mb-1 block text-sm font-semibold">Début de couverture souhaité</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}" min="{{ today()->format('Y-m-d') }}" class="input">
        @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="mt-6">
      <label class="mb-1 block text-sm font-semibold">Informations complémentaires</label>
      <textarea name="notes" rows="3" maxlength="1000" placeholder="Ex : conducteur de moins de 3 ans de permis, garage fermé la nuit…"
                class="w-full rounded-2xl border border-brand-700/20 bg-white px-5 py-3 outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-400/40">{{ old('notes') }}</textarea>
      @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-8 flex gap-3">
      <button class="btn-primary">Envoyer ma demande</button>
      <a href="{{ route('client.insurance.index') }}" class="btn-accent">Annuler</a>
    </div>
  </form>
@endif
@endsection