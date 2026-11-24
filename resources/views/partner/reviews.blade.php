@extends('layouts.app')
@section('title', 'Avis')

@section('content')
<h1 class="text-3xl font-extrabold text-brand-700">Avis de vos clients</h1>

<div class="card mt-6 max-w-sm">
  <p class="text-sm font-semibold text-brand-900/60">Note moyenne</p>
  <p class="mt-2 text-4xl font-extrabold text-brand-700">
    {{ $average ? number_format($average, 1, ',', '') . ' / 5' : '—' }}
  </p>
  <p class="text-xs text-brand-900/50">{{ $reviews->count() }} avis</p>
</div>

@if ($reviews->isEmpty())
  <div class="card mt-6 py-14 text-center text-sm text-brand-900/60">Aucun avis pour le moment.</div>
@else
  <div class="mt-6 space-y-4">
    @foreach ($reviews as $r)
      <article class="card">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <p class="font-bold text-amber-500">{{ $r->stars() }}</p>
          <p class="text-xs text-brand-900/50">{{ \Illuminate\Support\Str::before($r->client->name, ' ') }} · {{ $r->created_at->locale('fr')->translatedFormat('d F Y') }}</p>
        </div>
        @if ($r->comment)<p class="mt-2 text-sm text-brand-900/80">« {{ $r->comment }} »</p>@endif
      </article>
    @endforeach
  </div>
@endif
@endsection