@extends('layouts.app')
@section('title', 'Paramètres')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Paramètres</h1>
<p class="mt-1 text-brand-900/70">Canaux d'envoi des alertes d'échéance.</p>

<form method="POST" action="{{ route('admin.settings.update') }}" class="card mt-8 max-w-2xl !p-8">
  @csrf

  <p class="mb-4 text-sm font-semibold text-brand-900/60">Les notifications dans l'application sont toujours actives.</p>

  <label class="flex items-center justify-between gap-4 border-t border-brand-700/10 py-4">
    <span><span class="block font-bold">SMS</span><span class="text-sm text-brand-900/60">Rappels envoyés au numéro de téléphone du client.</span></span>
    <input type="checkbox" name="alerts_sms" value="1" class="h-5 w-5 rounded border-brand-700/30" @checked($sms)>
  </label>

  <label class="flex items-center justify-between gap-4 border-t border-brand-700/10 py-4">
    <span><span class="block font-bold">E-mail</span><span class="text-sm text-brand-900/60">Uniquement pour les clients qui ont renseigné une adresse.</span></span>
    <input type="checkbox" name="alerts_mail" value="1" class="h-5 w-5 rounded border-brand-700/30" @checked($mail)>
  </label>

  <label class="flex items-center justify-between gap-4 border-t border-brand-700/10 py-4 opacity-50">
    <span><span class="block font-bold">Notifications push</span><span class="text-sm text-brand-900/60">Disponible avec l'application mobile.</span></span>
    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">Bientôt</span>
  </label>

  <button class="btn-primary mt-6">Enregistrer</button>
</form>
@endsection