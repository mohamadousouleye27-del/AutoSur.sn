@extends('layouts.app')
@section('title', 'Documents')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Documents</h1>
<p class="mt-1 text-sm text-brand-900/70">Vous voyez les informations des documents, pas leur contenu : il reste privé.</p>

<form method="GET" class="mt-6 flex flex-wrap gap-3">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Titre ou propriétaire…" class="input !w-auto !py-2 text-sm">
  <select name="type" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm">
    <option value="">Tous les types</option>
    @foreach (\App\Models\Document::TYPES as $key => $label)
      <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
    @endforeach
  </select>
  <button class="btn-accent !py-2 text-sm">Rechercher</button>
</form>

<div class="card mt-6 overflow-x-auto !p-0">
  <table class="w-full text-left text-sm">
    <thead class="border-b border-brand-700/10 text-xs uppercase text-brand-900/50">
      <tr><th class="px-5 py-3">Titre</th><th class="px-5 py-3">Type</th><th class="px-5 py-3">Propriétaire</th><th class="px-5 py-3">Taille</th><th class="px-5 py-3">Expiration</th><th class="px-5 py-3"></th></tr>
    </thead>
    <tbody>
      @forelse ($documents as $d)
        <tr class="border-b border-brand-700/5">
          <td class="px-5 py-3 font-bold">{{ $d->title }}</td>
          <td class="px-5 py-3">{{ $d->typeLabel() }}</td>
          <td class="px-5 py-3">{{ $d->user->name }} <span class="text-brand-900/50">· {{ $d->vehicle?->plate ?? 'sans véhicule' }}</span></td>
          <td class="px-5 py-3">{{ $d->humanSize() }}</td>
          <td class="px-5 py-3">{{ $d->expires_at?->format('d/m/Y') ?? '—' }}</td>
          <td class="px-5 py-3 text-right">
            <form method="POST" action="{{ route('admin.documents.destroy', $d) }}" onsubmit="return confirm('Supprimer définitivement ce document ?')">
              @csrf @method('DELETE')
              <button class="rounded-full border border-red-200 px-4 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">Supprimer</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-brand-900/60">Aucun document trouvé.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@include('partials.pager', ['paginator' => $documents])
@endsection