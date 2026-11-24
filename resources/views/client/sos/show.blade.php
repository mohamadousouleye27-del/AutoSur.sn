@extends('layouts.app')
@section('title', 'Suivi SOS')

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

@section('content')
@php $b = $sos->badge(); @endphp

<a href="{{ route('client.sos.index') }}" class="text-sm font-semibold text-brand-700 underline">← SOS Panne</a>

<div class="mt-3 flex flex-wrap items-center justify-between gap-3">
  <h1 class="text-3xl font-extrabold text-brand-700">{{ $sos->problemLabel() }}</h1>
  <span id="sos-badge" class="rounded-full px-3 py-1 text-xs font-bold {{ $b['class'] }}">{{ $b['label'] }}</span>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
  <section class="card">
    <h2 class="text-lg font-extrabold">Votre demande</h2>
    <dl class="mt-4 space-y-3 text-sm">
      <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Envoyée</dt><dd class="font-bold">{{ $sos->created_at->locale('fr')->translatedFormat('d F Y à H:i') }}</dd></div>
      @if ($sos->vehicle)
        <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Véhicule</dt><dd class="font-bold">{{ $sos->vehicle->brand }} {{ $sos->vehicle->model }} · {{ $sos->vehicle->plate }}</dd></div>
      @endif
      @if ($sos->landmark)
        <div class="flex justify-between gap-4"><dt class="text-brand-900/60">Repère</dt><dd class="text-right font-bold">{{ $sos->landmark }}</dd></div>
      @endif
      @if ($sos->description)
        <div><dt class="text-brand-900/60">Détails</dt><dd>{{ $sos->description }}</dd></div>
      @endif
    </dl>

    <div id="driver-box" class="mt-5 rounded-2xl bg-brand-200/70 p-4 text-sm {{ $sos->driver ? '' : 'hidden' }}">
      <p class="font-bold text-brand-700">Votre dépanneur</p>
      <p id="driver-name" class="mt-1">{{ $sos->driver?->name }}</p>
      <a id="driver-phone" href="tel:{{ $sos->driver?->phone }}" class="font-bold text-brand-700 underline">{{ $sos->driver?->phone }}</a>
    </div>

    <p id="searching-note" class="mt-5 text-sm text-brand-900/60 {{ $sos->status === 'searching' ? '' : 'hidden' }}">
      Nous contactons les dépanneurs proches de vous. Cette page se met à jour automatiquement.
    </p>


    @if ($sos->status === 'completed' && $sos->driver_id)
  <section class="card mt-6 max-w-xl">
    <h2 class="text-lg font-extrabold">Votre avis sur le dépanneur</h2>
    @if ($sos->rating)
        <p class="mt-2 text-sm text-brand-900/70"><span class="font-bold text-amber-500">{{ $sos->rating->stars() }}</span>@if ($sos->rating->comment) · « {{ $sos->rating->comment }} »@endif</p>
        @else
        <div class="mt-3">
            @include('client.partials.rating-form', ['action' => route('client.ratings.sos', $sos), 'label' => 'Noter ce dépanneur'])
        </div>
        @endif
    </section>
    @endif

    @if ($sos->isActive())
      <form method="POST" action="{{ route('client.sos.destroy', $sos) }}" class="mt-5" onsubmit="return confirm('Annuler la demande SOS ?')">
        @csrf @method('DELETE')
        <button class="rounded-full border border-red-200 px-6 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Annuler la demande</button>
      </form>
    @endif
  </section>

  <section class="card overflow-hidden !p-0">
    @if ($sos->embedUrl())
      <iframe src="{{ $sos->embedUrl() }}" class="h-80 w-full border-0 lg:h-full lg:min-h-[22rem]" loading="lazy" title="Votre position"></iframe>
      <div class="px-5 py-3 text-xs">
        <a class="font-semibold text-brand-700 underline" target="_blank" rel="noopener"
           href="https://www.openstreetmap.org/?mlat={{ $sos->latitude }}&mlon={{ $sos->longitude }}#map=17/{{ $sos->latitude }}/{{ $sos->longitude }}">Ouvrir la carte en grand</a>
      </div>
    @else
      <div class="flex h-full min-h-[14rem] items-center justify-center p-6 text-center text-sm text-brand-900/60">
        Position GPS non transmise : le dépanneur se fiera à votre repère écrit.
      </div>
    @endif
  </section>
</div>

@if ($sos->isActive())
<script>
  const initial = @json($sos->status);
  const url = @json(route('client.sos.status', $sos));

  async function poll() {
    try {
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) return;
      const data = await res.json();

      if (data.status !== initial) { location.reload(); return; }

      if (data.driver) {
        document.getElementById('driver-box').classList.remove('hidden');
        document.getElementById('driver-name').textContent = data.driver.name;
        const phone = document.getElementById('driver-phone');
        phone.textContent = data.driver.phone;
        phone.href = 'tel:' + data.driver.phone;
      }
    } catch (e) { /* on réessaie au prochain passage */ }
  }

  setInterval(poll, 8000);
</script>
@endif
@endsection