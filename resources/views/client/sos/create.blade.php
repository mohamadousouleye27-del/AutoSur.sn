@extends('layouts.app')
@section('title', 'Demander un dépanneur')

@section('content')
<a href="{{ route('client.sos.index') }}" class="text-sm font-semibold text-brand-700 underline">← Retour</a>
<h1 class="mt-3 text-3xl font-extrabold text-brand-700">Demander un dépanneur</h1>

<form method="POST" action="{{ route('client.sos.store') }}" class="card mt-6 max-w-3xl !p-8">
  @csrf
  <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
  <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

  {{-- Localisation --}}
  <div class="rounded-2xl bg-brand-400/15 p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <p id="geo-status" class="text-sm font-semibold text-brand-900">Localisation en cours…</p>
      <button type="button" id="geo-btn" class="btn-accent !py-2 text-sm">Me localiser</button>
    </div>
  </div>

  <div class="mt-6">
    <p class="mb-2 text-sm font-semibold">Quel est le problème ? *</p>
    <div class="grid gap-2 sm:grid-cols-2">
      @foreach (\App\Models\SosRequest::PROBLEMS as $key => $label)
        <label class="cursor-pointer">
          <input type="radio" name="problem" value="{{ $key }}" class="peer sr-only" @checked(old('problem') === $key) required>
          <span class="block rounded-2xl border border-brand-700/20 px-5 py-3 text-sm font-semibold peer-checked:border-brand-700 peer-checked:bg-brand-200">{{ $label }}</span>
        </label>
      @endforeach
    </div>
    @error('problem') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="mt-6">
    <label class="mb-1 block text-sm font-semibold">Véhicule concerné</label>
    <select name="vehicle_id" class="input">
      <option value="">Non précisé</option>
      @foreach ($vehicles as $v)
        <option value="{{ $v->id }}" @selected((string) old('vehicle_id') === (string) $v->id)>{{ $v->plate }} · {{ $v->brand }} {{ $v->model }}</option>
      @endforeach
    </select>
    @error('vehicle_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="mt-6">
    <label class="mb-1 block text-sm font-semibold">Repère pour vous retrouver</label>
    <input type="text" name="landmark" value="{{ old('landmark') }}" maxlength="150" placeholder="Ex : autoroute à péage, sortie Rufisque, devant la station Total" class="input">
    @error('landmark') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    @error('latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="mt-6">
    <label class="mb-1 block text-sm font-semibold">Détails (facultatif)</label>
    <textarea name="description" rows="2" maxlength="500" class="w-full rounded-2xl border border-brand-700/20 bg-white px-5 py-3 outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-400/40">{{ old('description') }}</textarea>
  </div>

  <button class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-red-600 px-8 py-4 text-lg font-extrabold text-white shadow-lg shadow-red-600/30 transition hover:bg-red-700 active:scale-95">
    Envoyer ma demande SOS
  </button>
</form>

<script>
  const statusEl = document.getElementById('geo-status');
  const latEl = document.getElementById('latitude');
  const lonEl = document.getElementById('longitude');

  function locate() {
    if (!navigator.geolocation) {
      statusEl.textContent = "La localisation n'est pas disponible sur cet appareil. Décrivez où vous êtes ci-dessous.";
      return;
    }
    statusEl.textContent = 'Localisation en cours…';
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        latEl.value = pos.coords.latitude.toFixed(7);
        lonEl.value = pos.coords.longitude.toFixed(7);
        statusEl.textContent = '✔ Position détectée (précision environ ' + Math.round(pos.coords.accuracy) + ' m)';
      },
      () => {
        latEl.value = ''; lonEl.value = '';
        statusEl.textContent = 'Position non détectée. Autorisez la localisation ou décrivez où vous êtes ci-dessous.';
      },
      { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
    );
  }

  document.getElementById('geo-btn').addEventListener('click', locate);
  locate();
</script>
@endsection