@extends('layouts.app')
@section('title', 'Véhicules')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Véhicules</h1>

<form method="GET" class="mt-6 flex flex-wrap gap-3">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Immatriculation, marque, propriétaire…" class="input !w-auto !py-2 text-sm">
  <button class="btn-accent !py-2 text-sm">Rechercher</button>
</form>

<div class="card mt-6 overflow-x-auto !p-0">
  <table class="w-full text-left text-sm">
    <thead class="border-b border-brand-700/10 text-xs uppercase text-brand-900/50">
      <tr><th class="px-5 py-3">Véhicule</th><th class="px-5 py-3">Immatriculation</th><th class="px-5 py-3">Propriétaire</th><th class="px-5 py-3">Ajouté</th><th class="px-5 py-3"></th></tr>
    </thead>
    <tbody>
      @forelse ($vehicles as $v)
        <tr class="border-b border-brand-700/5">
          <td class="px-5 py-3 font-bold">{{ $v->brand }} {{ $v->model }} {{ $v->year }}</td>
          <td class="px-5 py-3">{{ $v->plate }}</td>
          <td class="px-5 py-3">{{ $v->user->name }} <span class="text-brand-900/50">· {{ $v->user->phone }}</span></td>
          <td class="px-5 py-3 text-brand-900/60">{{ $v->created_at->format('d/m/Y') }}</td>
          <td class="px-5 py-3 text-right">
            <form method="POST" action="{{ route('admin.vehicles.destroy', $v) }}" onsubmit="return confirm('Supprimer ce véhicule ? Ses rendez-vous et demandes de devis seront supprimés aussi.')">
              @csrf @method('DELETE')
              <button class="rounded-full border border-red-200 px-4 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">Supprimer</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-brand-900/60">Aucun véhicule trouvé.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@include('partials.pager', ['paginator' => $vehicles])
@endsection