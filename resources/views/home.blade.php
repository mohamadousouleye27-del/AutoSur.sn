@extends('layouts.public')
@section('title', 'AutoSur : Gérez votre voiture en toute sérénité')

@section('content')

@php $img = fn (string $id, int $w = 800, int $h = 500, string $crop = 'fill') => app(\App\Services\ImageStorage::class)->url($id, $w, $h, $crop); @endphp

{{-- HERO --}}
<section class="mx-auto max-w-6xl px-6 pt-10">
  <div class="grid items-center gap-10 md:grid-cols-2">
    <div>
      <h1 class="text-4xl font-extrabold leading-[1.1] tracking-tight text-brand-900 md:text-6xl">
        Gérez votre voiture en toute sérénité
      </h1>
      <p class="mt-5 max-w-md text-brand-900/70">
        Documents, rappels, assurance, visite technique et dépannage SOS réunis au même endroit.
      </p>
      <span class="mt-6 inline-flex rounded-full bg-brand-200 px-6 py-3 font-bold text-brand-900">
        Disponible partout au Sénégal
      </span>
    </div>

    <div class="relative flex h-72 items-center justify-center overflow-hidden rounded-[2rem] bg-brand-400 md:h-[22rem]">
      <img src="{{ asset('images/hero-car.png') }}" alt="Voiture bleue"
        class="h-full w-full object-contain p-3 drop-shadow-xl md:p-5">
    </div>
</section>

{{-- RECHERCHE --}}
<section id="partenaires" class="relative z-10 mx-auto mt-16 max-w-6xl scroll-mt-24 px-6 md:mt-20">
  <form action="#" class="flex flex-col gap-2 rounded-[2rem] bg-brand-700 p-2.5 shadow-xl shadow-brand-700/25 md:flex-row md:items-center md:rounded-full">
    <div class="flex flex-1 flex-col divide-y divide-brand-700/10 rounded-[1.5rem] bg-white md:flex-row md:divide-x md:divide-y-0 md:rounded-full">
      <label class="flex flex-1 items-center gap-3 px-6 py-4">
        <svg class="h-5 w-5 shrink-0 text-brand-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
        <input type="text" placeholder="Lieu (ex : Dakar, Pikine)" class="w-full bg-transparent text-sm outline-none placeholder:text-brand-900/60">
      </label>
      <label class="flex flex-1 items-center gap-3 px-6 py-4">
        <svg class="h-5 w-5 shrink-0 text-brand-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
        <input type="date" class="w-full bg-transparent text-sm text-brand-900/80 outline-none">
      </label>
      <label class="flex flex-1 items-center gap-3 px-6 py-4">
        <select class="w-full appearance-none bg-transparent text-sm text-brand-900/80 outline-none">
          <option>Type de partenaire</option>
          <option>Garage</option><option>Dépanneur</option><option>Assurance</option><option>Visite technique</option>
        </select>
        <svg class="h-4 w-4 shrink-0 text-brand-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
      </label>
    </div>
    <button type="button" class="btn-accent !px-10 !py-4">Rechercher</button>
  </form>
</section>

{{-- SERVICES --}}
<section id="services" class="mx-auto max-w-6xl scroll-mt-24 px-6 pb-20 pt-20 md:pt-24">
  <h2 class="text-3xl font-extrabold text-brand-700">Tout ce dont vous avez besoin</h2>
  <p class="mt-2 text-brand-900/70">Six services réunis dans une seule plateforme.</p>

  @php
    $services = [
      ['vehicle',   'Mes véhicules',         'Une fiche par véhicule : immatriculation, châssis, assurance, historique.'],
      ['documents', 'Coffre-fort numérique', 'Carte grise, attestation, factures : tout est rangé et accessible.'],
      ['alerts',    'Alertes intelligentes', 'Notifications push, SMS et e-mail avant chaque échéance.'],
      ['insurance', 'Assurance auto',        'Demandez un devis et comparez les offres de nos partenaires.'],
      ['inspection','Visite technique',      'Prenez rendez-vous et suivez le statut de votre contrôle.'],
      ['sos',       'SOS Panne',             'Un dépanneur proche de vous, localisé en temps réel.'],
    ];
  @endphp

  <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($services as [$icon, $title, $text])
      <article class="rounded-3xl border border-brand-700/10 bg-white p-2.5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="relative flex h-44 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-200/50 via-brand-50 to-brand-400/30">
          <div class="absolute h-32 w-32 rounded-full bg-white/70"></div>
          <svg class="relative h-24 w-24 text-brand-700" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 96 96" aria-hidden="true">
            @switch ($icon)
              @case('vehicle')
                <path d="m18 54 5-16a8 8 0 0 1 7-5h36a8 8 0 0 1 7 5l5 16v18H18V54Z" fill="currentColor" opacity=".1"/>
                <path d="m18 54 5-16a8 8 0 0 1 7-5h36a8 8 0 0 1 7 5l5 16v18H18V54Z"/>
                <path d="M25 51h46M31 34l-5 17m39-17 5 17M18 59h12m36 0h12"/>
                <circle cx="30" cy="68" r="6" fill="currentColor"/>
                <circle cx="66" cy="68" r="6" fill="currentColor"/>
                <path d="M35 42h26" stroke="#95B1EE" stroke-width="5"/>
                @break
              @case('documents')
                <path d="M28 17h27l14 14v46H28V17Z" fill="white"/>
                <path d="M55 17v15h14M37 45h23M37 54h17"/>
                <rect x="48" y="58" width="30" height="23" rx="6" fill="#E7F1A8"/>
                <path d="M57 58v-4a6 6 0 0 1 12 0v4m-6 9v5"/>
                @break
              @case('alerts')
                <path d="M25 65h46l-6-8V43a17 17 0 0 0-34 0v14l-6 8Z" fill="white"/>
                <path d="M25 65h46l-6-8V43a17 17 0 0 0-34 0v14l-6 8Zm17 7a7 7 0 0 0 12 0"/>
                <circle cx="70" cy="28" r="13" fill="#E7F1A8"/>
                <path d="M70 21v8m0 5h.01"/>
                @break
              @case('insurance')
                <path d="M48 15 73 25v19c0 17-11 28-25 37C34 72 23 61 23 44V25l25-10Z" fill="white"/>
                <path d="m36 48 8 8 17-19"/>
                <path d="m34 68-11 8 2-14m37 6 11 8-2-14" stroke="#95B1EE"/>
                @break
              @case('inspection')
                <rect x="19" y="25" width="58" height="53" rx="8" fill="white"/>
                <path d="M19 41h58M33 18v14m30-14v14"/>
                <path d="M32 52h8m-8 12h8" stroke="#95B1EE" stroke-width="4"/>
                <circle cx="62" cy="62" r="13" fill="#E7F1A8"/>
                <path d="m56 62 4 4 8-9"/>
                @break
              @case('sos')
                <path d="M48 78S25 56 25 39a23 23 0 1 1 46 0C71 56 48 78 48 78Z" fill="white"/>
                <circle cx="48" cy="39" r="9" fill="#E7F1A8"/>
                <path d="m37 67-8 8m25-8 8 8m-19-3 5-8 5 8"/>
                <path d="M42 75h12"/>
                @break
            @endswitch
          </svg>
          <span class="absolute right-3 top-3 rounded-full bg-brand-200 px-3 py-1 text-xs font-bold text-brand-900">Disponible</span>
        </div>
        <div class="px-3 pb-3 pt-4">
          <h3 class="text-lg font-extrabold text-brand-900">{{ $title }}</h3>
          <p class="mt-1 text-sm text-brand-900/70">{{ $text }}</p>
        </div>
      </article>
    @endforeach
  </div>
</section>

{{-- COMMENT ÇA MARCHE --}}
<section class="bg-brand-700 py-20 text-white">
  <div class="mx-auto max-w-6xl px-6">
    <h2 class="text-3xl font-extrabold">Comment ça marche ?</h2>
    <div class="mt-10 grid gap-8 md:grid-cols-3">
      @foreach ([['1','Inscrivez-vous','Avec votre numéro de téléphone et un code reçu par SMS.'],
                 ['2','Ajoutez vos véhicules','Renseignez vos véhicules et importez vos documents.'],
                 ['3','Laissez-nous gérer','Recevez vos rappels et accédez aux services en un clic.']] as [$n,$t,$d])
        <div>
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-200 text-xl font-extrabold text-brand-700">{{ $n }}</div>
          <h3 class="mt-4 text-xl font-bold">{{ $t }}</h3>
          <p class="mt-2 text-white/75">{{ $d }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- PARTENAIRES --}}
<section class="mx-auto max-w-6xl px-6 py-20">
  <div class="rounded-[2rem] bg-brand-200 p-10 text-center md:p-14">
    <h2 class="text-3xl font-extrabold text-brand-700">Garage, dépanneur ou assureur ?</h2>
    <p class="mx-auto mt-3 max-w-xl text-brand-900/70">
      Rejoignez AutoSur pour recevoir de nouveaux clients et gérer vos interventions depuis votre espace dédié.
    </p>
    <a href="{{ route('register') }}" class="btn-primary mt-7">Devenir partenaire</a>
  </div>
</section>

<footer id="contact" class="border-t border-brand-700/10 py-10 text-center text-sm text-brand-900/60">
  <div class="flex justify-center"><x-logo /></div>
  <p class="mt-3">Dakar, Sénégal · contact@autosur.sn</p>
  <p class="mt-4">© {{ date('Y') }} AutoSur. Tous droits réservés.</p>
</footer>

@endsection