@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Utilisateurs</h1>

<form method="GET" class="mt-6 flex flex-wrap gap-3">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, téléphone ou e-mail" class="input !w-auto !py-2 text-sm">
  <select name="role" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm">
    <option value="">Tous les rôles</option>
    @foreach (\App\Models\User::ROLE_LABELS as $key => $label)
      <option value="{{ $key }}" @selected(request('role') === $key)>{{ $label }}</option>
    @endforeach
  </select>
  <button class="btn-accent !py-2 text-sm">Rechercher</button>
</form>

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

<div class="card mt-6 overflow-x-auto !p-0">
  <table class="w-full text-left text-sm">
    <thead class="border-b border-brand-700/10 text-xs uppercase text-brand-900/50">
      <tr>
        <th class="px-5 py-3">Nom</th><th class="px-5 py-3">Téléphone</th><th class="px-5 py-3">Rôle</th>
        <th class="px-5 py-3">Statut</th><th class="px-5 py-3">Inscrit</th><th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($users as $u)
        <tr class="border-b border-brand-700/5">
          <td class="px-5 py-3 font-bold">{{ $u->name }}</td>
          <td class="px-5 py-3">{{ $u->phone }}</td>
          <td class="px-5 py-3">{{ \App\Models\User::ROLE_LABELS[$u->role] ?? $u->role }}</td>
          <td class="px-5 py-3">
            @if (! $u->is_active)
              <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">Suspendu</span>
            @elseif ($u->isPartner() && $u->partner_status !== 'approved')
              <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">{{ $u->partner_status === 'rejected' ? 'Refusé' : 'À valider' }}</span>
            @else
              <span class="rounded-full bg-brand-200 px-3 py-1 text-xs font-bold text-brand-700">Actif</span>
            @endif
          </td>
          <td class="px-5 py-3 text-brand-900/60">{{ $u->created_at->format('d/m/Y') }}</td>
          <td class="px-5 py-3 text-right">
            @if ($u->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.toggle', $u) }}" onsubmit="return confirm('{{ $u->is_active ? 'Suspendre' : 'Réactiver' }} ce compte ?')">
                @csrf
                <button class="rounded-full border px-4 py-1.5 text-xs font-semibold transition {{ $u->is_active ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-brand-700/20 text-brand-700 hover:bg-brand-200/60' }}">
                  {{ $u->is_active ? 'Suspendre' : 'Réactiver' }}
                </button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-brand-900/60">Aucun utilisateur trouvé.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4 flex items-center justify-between text-sm">
  <span class="text-brand-900/60">Page {{ $users->currentPage() }} / {{ $users->lastPage() }} · {{ $users->total() }} utilisateur(s)</span>
  <div class="flex gap-2">
    @if ($users->previousPageUrl())<a href="{{ $users->previousPageUrl() }}" class="btn-accent !py-2 text-sm">← Précédent</a>@endif
    @if ($users->nextPageUrl())<a href="{{ $users->nextPageUrl() }}" class="btn-accent !py-2 text-sm">Suivant →</a>@endif
  </div>
</div>
@endsection