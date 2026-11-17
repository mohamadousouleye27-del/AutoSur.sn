@extends('layouts.app')
@section('title', 'Prendre rendez-vous')

@section('content')
<a href="{{ route('client.inspections.index') }}" class="text-sm font-semibold text-brand-700 underline">← Retour</a>
<h1 class="mt-3 text-3xl font-extrabold text-brand-700">Prendre rendez-vous</h1>

@if ($vehicles->isEmpty())
  <div class="card mt-6 max-w-3xl py-12 text-center">
    <p class="font-bold text-brand-900">Ajoutez d'abord un véhicule</p>
    <a href="{{ route('client.vehicles.create') }}" class="btn-primary mt-6">Ajouter un véhicule</a>
  </div>
@else
  <form method="POST" action="{{ route('client.inspections.store') }}" class="card mt-6 max-w-3xl !p-8">
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
      <p class="mb-2 text-sm font-semibold">Type de véhicule * <span class="font-normal text-brand-900/60">(détermine les frais de service)</span></p>
      <div class="grid gap-2">
        @foreach (\App\Models\InspectionAppointment::CATEGORIES as $key => $c)
          <label class="cursor-pointer">
            <input type="radio" name="category" value="{{ $key }}" class="peer sr-only" @checked(old('category', 'particulier') === $key)>
            <span class="flex items-center justify-between rounded-2xl border border-brand-700/20 px-5 py-3 text-sm font-semibold peer-checked:border-brand-700 peer-checked:bg-brand-200">
              {{ $c['label'] }}
              <span class="text-brand-700">{{ number_format($c['fee'], 0, ',', ' ') }} FCFA</span>
            </span>
          </label>
        @endforeach
      </div>
      @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-semibold">Date souhaitée *</label>
        <input type="date" name="scheduled_date" min="{{ today()->addDay()->format('Y-m-d') }}" value="{{ old('scheduled_date') }}" class="input" required>
        @error('scheduled_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="mb-1 block text-sm font-semibold">Créneau *</label>
        <select name="time_slot" class="input" required>
          @foreach (\App\Models\InspectionAppointment::TIME_SLOTS as $key => $label)
            <option value="{{ $key }}" @selected(old('time_slot', 'morning') === $key)>{{ $label }}</option>
          @endforeach
        </select>
        @error('time_slot') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="mt-6">
      <label class="mb-1 block text-sm font-semibold">Ville ou quartier *</label>
      <input type="text" name="city" value="{{ old('city') }}" placeholder="Ex : Dakar, Pikine, Thiès" class="input" required>
      @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6">
      <label class="mb-1 block text-sm font-semibold">Remarques</label>
      <textarea name="notes" rows="3" maxlength="500" placeholder="Ex : préférence de centre, disponibilité…"
                class="w-full rounded-2xl border border-brand-700/20 bg-white px-5 py-3 outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-400/40">{{ old('notes') }}</textarea>
    </div>

    <div class="mt-8 flex gap-3">
      <button class="btn-primary">Envoyer la demande</button>
      <a href="{{ route('client.inspections.index') }}" class="btn-accent">Annuler</a>
    </div>
  </form>
@endif
@endsection