@extends('layouts.public')
@section('title', 'AutoSur : Gérez votre voiture en toute sérénité')

@section('content')

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

    <div class="relative h-72 rounded-[2rem] bg-brand-400 md:h-[22rem]">
      <x-photo src="hero-car.png" alt="Voiture"
         class="absolute bottom-6 right-4 h-[88%] w-auto max-w-none -translate-x-[12%] object-contain drop-shadow-xl md:right-6 md:-translate-x-[30%]" />
    </div>
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
      ['vehicules.jpg',  'Mes véhicules',         'Une fiche par véhicule : immatriculation, châssis, assurance, historique.'],
      ['coffre-fort.jpg','Coffre-fort numérique', 'Carte grise, attestation, factures : tout est rangé et accessible.'],
      ['alertes.jpg',    'Alertes intelligentes', 'Notifications push, SMS et e-mail avant chaque échéance.'],
      ['assurance.jpg',  'Assurance auto',        'Demandez un devis et comparez les offres de nos partenaires.'],
      ['visite.jpg',     'Visite technique',      'Prenez rendez-vous et suivez le statut de votre contrôle.'],
      ['sos.jpg',        'SOS Panne',             'Un dépanneur proche de vous, localisé en temps réel.'],
    ];
  @endphp

  <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($services as [$img, $title, $text])
      <article class="rounded-3xl border border-brand-700/10 bg-white p-2.5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="relative">
          <x-photo :src="$img" :alt="$title" class="h-44 w-full rounded-2xl object-cover" />
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