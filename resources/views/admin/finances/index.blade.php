@extends('layouts.app')
@section('title', 'Suivi comptable')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-brand-700">Suivi comptable</h1>
    <p class="mt-1 text-brand-900/70">Montants estimés à partir des offres acceptées et des visites effectuées.</p>
  </div>
  <form method="GET" class="flex items-center gap-2">
    <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm">
    @if ($month)<a href="{{ route('admin.finances.index') }}" class="text-sm font-semibold text-brand-700 underline">Tout afficher</a>@endif
  </form>
</div>

<div class="mt-8 grid gap-5 sm:grid-cols-3">
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Commissions d'assurance</p>
    <p class="mt-2 text-2xl font-extrabold text-brand-700">{{ number_format($totalCommission, 0, ',', ' ') }} FCFA</p></div>
  <div class="card"><p class="text-sm font-semibold text-brand-900/60">Frais visites techniques</p>
    <p class="mt-2 text-2xl font-extrabold text-brand-700">{{ number_format($totalFees, 0, ',', ' ') }} FCFA</p></div>
  <div class="card !bg-brand-200/60"><p class="text-sm font-semibold text-brand-900/70">Total estimé</p>
    <p class="mt-2 text-2xl font-extrabold text-brand-700">{{ number_format($totalCommission + $totalFees, 0, ',', ' ') }} FCFA</p></div>
</div>

<h2 class="mt-10 text-xl font-extrabold">Commissions d'assurance</h2>
<div class="mt-4 space-y-3">
  @forelse ($offers as $o)
    <div class="card flex flex-wrap items-center justify-between gap-3">
      <div>
        <p class="font-bold">{{ $o->insurer->name }}</p>
        <p class="text-sm text-brand-900/60">{{ $o->quote->vehicle->brand }} {{ $o->quote->vehicle->model }} · prime {{ $o->formattedPremium() }} · {{ $o->updated_at->format('d/m/Y') }}</p>
      </div>
      <p class="font-extrabold text-brand-700">{{ $o->formattedCommission() }} <span class="text-xs font-medium text-brand-900/50">({{ rtrim(rtrim(number_format($o->commission_rate, 2), '0'), '.') }} %)</span></p>
    </div>
  @empty
    <p class="text-sm text-brand-900/60">Aucune commission sur cette période.</p>
  @endforelse
</div>

<h2 class="mt-10 text-xl font-extrabold">Frais de service des visites techniques</h2>
<div class="mt-4 space-y-3">
  @forelse ($visits as $v)
    <div class="card flex flex-wrap items-center justify-between gap-3">
      <div>
        <p class="font-bold">{{ $v->vehicle->brand }} {{ $v->vehicle->model }} · {{ $v->vehicle->plate }}</p>
        <p class="text-sm text-brand-900/60">{{ $v->garage?->name ?? 'Garage non renseigné' }} · {{ $v->updated_at->format('d/m/Y') }}</p>
      </div>
      <p class="font-extrabold text-brand-700">{{ $v->formattedFee() }}</p>
    </div>
  @empty
    <p class="text-sm text-brand-900/60">Aucune visite effectuée sur cette période.</p>
  @endforelse
</div>

<p class="mt-8 text-xs text-brand-900/50">Ces montants sont des estimations : le paiement en ligne (Wave / Orange Money) n'est pas encore branché.</p>
@endsection