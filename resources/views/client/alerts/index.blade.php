@extends('layouts.app')
@section('title', 'Alertes')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-brand-700">Alertes</h1>
    <p class="mt-1 text-brand-900/70">Rappels sur vos assurances, visites techniques et documents.</p>
  </div>

  @if (auth()->user()->unreadNotifications()->count())
    <form method="POST" action="{{ route('client.alerts.read-all') }}">
      @csrf
      <button class="btn-accent !py-2 text-sm">Tout marquer comme lu</button>
    </form>
  @endif
</div>

@if ($notifications->isEmpty())
  <div class="card mt-8 py-16 text-center">
    <p class="text-lg font-bold text-brand-900">Aucune alerte pour le moment</p>
    <p class="mt-1 text-sm text-brand-900/60">Vous serez prévenu 30 jours, 7 jours et le jour de chaque échéance.</p>
  </div>
@else
  <div class="mt-8 space-y-3">
    @foreach ($notifications as $n)
      @php
        $stage = $n->data['stage'] ?? '';
        $tone = match ($stage) {
          'expired'  => 'bg-red-100 text-red-700',
          'j0', 'j7' => 'bg-amber-100 text-amber-800',
          default    => 'bg-brand-200 text-brand-700',
        };
        $label = match ($stage) {
          'expired' => 'Expiré',
          'j0'      => "Aujourd'hui",
          'j7'      => 'Urgent',
          default   => 'À prévoir',
          'info'    => 'Nouveau',
        };
      @endphp
      <a href="{{ route('client.alerts.read', $n->id) }}"
         class="card flex items-start gap-4 transition hover:shadow-md {{ $n->read_at ? 'opacity-70' : 'border-brand-700/30' }}">
        <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $n->read_at ? 'bg-transparent' : 'bg-brand-700' }}"></span>
        <div class="min-w-0 flex-1">
          <p class="font-semibold text-brand-900">{{ $n->data['message'] }}</p>
          <p class="mt-1 text-xs text-brand-900/50">{{ $n->created_at->locale('fr')->diffForHumans() }}</p>
        </div>
        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $tone }}">{{ $label }}</span>
      </a>
    @endforeach
  </div>
@endif
@endsection