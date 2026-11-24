@extends('layouts.app')
@section('title', 'Avis')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Avis des clients</h1>

<nav class="mt-6 flex gap-2">
  <a href="{{ route('admin.reviews.index') }}" class="rounded-full px-5 py-2 text-sm font-semibold {{ request('filter') !== 'low' ? 'bg-brand-200 text-brand-700' : 'bg-white text-brand-900/70 hover:bg-brand-200/60' }}">Tous</a>
  <a href="{{ route('admin.reviews.index', ['filter' => 'low']) }}" class="rounded-full px-5 py-2 text-sm font-semibold {{ request('filter') === 'low' ? 'bg-brand-200 text-brand-700' : 'bg-white text-brand-900/70 hover:bg-brand-200/60' }}">Notes basses (1-2)</a>
</nav>

@if ($reviews->isEmpty())
  <div class="card mt-6 py-14 text-center text-sm text-brand-900/60">Aucun avis.</div>
@else
  <div class="mt-6 space-y-4">
    @foreach ($reviews as $r)
      <article class="card flex flex-wrap items-start justify-between gap-4">
        <div>
          <p class="font-bold text-amber-500">{{ $r->stars() }}
            <span class="ml-2 text-sm font-semibold text-brand-900">{{ $r->partner->name }}</span>
            <span class="text-xs font-medium text-brand-900/50">({{ \App\Models\User::ROLE_LABELS[$r->partner->role] ?? '' }})</span>
          </p>
          @if ($r->comment)<p class="mt-1 text-sm text-brand-900/80">« {{ $r->comment }} »</p>@endif
          <p class="mt-1 text-xs text-brand-900/50">Par {{ $r->client->name }} · {{ $r->created_at->format('d/m/Y') }}</p>
        </div>
        <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Supprimer cet avis ?')">
          @csrf @method('DELETE')
          <button class="rounded-full border border-red-200 px-4 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">Supprimer</button>
        </form>
      </article>
    @endforeach
  </div>
@endif

@include('partials.pager', ['paginator' => $reviews])
@endsection