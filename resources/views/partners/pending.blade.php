@extends('layouts.public')
@section('title', 'Validation en cours')

@section('content')
<section class="mx-auto max-w-xl px-6 py-16">
  <div class="card !p-10 text-center">
    @if ($status === 'rejected')
      <h1 class="text-2xl font-extrabold text-brand-700">Demande non retenue</h1>
      <p class="mt-3 text-sm text-brand-900/70">Votre compte partenaire n'a pas été validé. Pour plus d'informations, contactez AutoSur à contact@autosur.sn.</p>
    @else
      <h1 class="text-2xl font-extrabold text-brand-700">Validation en cours</h1>
      <p class="mt-3 text-sm text-brand-900/70">Votre compte partenaire est en cours d'examen par l'équipe AutoSur. Vous aurez accès à votre espace dès sa validation.</p>
    @endif
  </div>
</section>
@endsection