@extends('layouts.app')
@section('title', 'Paiement (simulation)')

@section('content')
<div class="mx-auto max-w-md">
  <div class="card !p-8 text-center">
    <span class="badge">Mode simulation</span>
    <h1 class="mt-4 text-2xl font-extrabold text-brand-700">Paiement de {{ $payment->formattedAmount() }}</h1>
    <p class="mt-2 text-sm text-brand-900/60">Aucun argent réel n'est débité. Cette page remplace Wave pendant le développement.</p>

    <form method="POST" action="{{ route('client.payments.simulate', $payment) }}" class="mt-6 space-y-3">
      @csrf
      <button name="outcome" value="paid" class="btn-primary w-full">Simuler un paiement réussi</button>
      <button name="outcome" value="failed" class="w-full rounded-full border border-red-200 px-6 py-3 font-semibold text-red-600 transition hover:bg-red-50">Simuler un échec</button>
    </form>
  </div>
</div>
@endsection