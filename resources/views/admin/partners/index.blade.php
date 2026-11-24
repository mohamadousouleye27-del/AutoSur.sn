@extends('layouts.app')
@section('title', 'Partenaires')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Partenaires</h1>
<p class="mt-1 text-brand-900/70">Validez les garages, dépanneurs et assureurs avant qu'ils ne reçoivent des demandes.</p>

@if ($errors->any())
  <p class="mt-4 rounded-2xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
@endif

@if ($partners->isEmpty())
  <div class="card mt-8 py-16 text-center text-sm text-brand-900/60">Aucun partenaire inscrit pour le moment.</div>
@else
  <div class="mt-8 space-y-4">
    @foreach ($partners as $p)
      @php
        $badge = match ($p->partner_status) {
          'approved' => ['Validé', 'bg-brand-200 text-brand-700'],
          'rejected' => ['Refusé', 'bg-red-100 text-red-700'],
          default    => ['À valider', 'bg-amber-100 text-amber-800'],
        };
      @endphp
      <article class="card {{ $p->partner_status === 'pending' ? '!border-amber-300' : '' }}">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <p class="text-lg font-extrabold text-brand-900">{{ $p->name }}</p>
            <p class="text-sm text-brand-900/70">{{ \App\Models\User::ROLE_LABELS[$p->role] }} · {{ $p->phone }}@if ($p->email) · {{ $p->email }}@endif</p>
            <p class="text-xs text-brand-900/40">Inscrit le {{ $p->created_at->format('d/m/Y') }}</p>
            @if (in_array($p->role, ['garage', 'depanneur']))
              <p class="text-xs font-semibold text-amber-600">{{ ($avg = $p->averageRating()) ? 'Note : ' . number_format($avg, 1, ',', '') . ' / 5' : 'Pas encore noté' }}</p>
            @endif
          </div>
          <span class="rounded-full px-3 py-1 text-xs font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
        </div>

        <div class="mt-4 flex flex-wrap items-end gap-3">
          @if ($p->partner_status !== 'approved')
            <form method="POST" action="{{ route('admin.partners.approve', $p) }}">@csrf
              <button class="btn-primary !py-2 text-sm">Valider</button>
            </form>
          @endif
          @if ($p->partner_status !== 'rejected')
            <form method="POST" action="{{ route('admin.partners.reject', $p) }}" onsubmit="return confirm('Refuser ce partenaire ? Son accès sera bloqué.')">@csrf
              <button class="rounded-full border border-red-200 px-5 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">{{ $p->partner_status === 'approved' ? 'Révoquer' : 'Refuser' }}</button>
            </form>
          @endif

          @if ($p->role === 'assurance')
            <form method="POST" action="{{ route('admin.partners.commission', $p) }}" class="ml-auto flex items-end gap-2">@csrf
              <div>
                <label class="mb-1 block text-xs font-semibold">Commission (5 à 12 %)</label>
                <input type="number" name="commission_rate" step="0.5" min="5" max="12" value="{{ rtrim(rtrim(number_format($p->commission_rate, 2, '.', ''), '0'), '.') }}" class="input !w-28 !py-2 text-sm">
              </div>
              <button class="btn-accent !py-2 text-sm">Enregistrer</button>
            </form>
          @endif
        </div>
      </article>
    @endforeach
  </div>
@endif
@endsection