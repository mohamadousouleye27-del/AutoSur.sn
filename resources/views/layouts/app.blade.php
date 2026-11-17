<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'AutoSur') · AutoSur</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-400/10">
@php
  $user = auth()->user();
  $menu = config('menus.' . $user->role, []);
  $unread = $user->unreadNotifications()->count();
@endphp



<div class="min-h-screen md:flex">

  {{-- Menu latéral (ordinateur) --}}
  <aside class="hidden w-64 shrink-0 flex-col border-r border-brand-700/10 bg-white p-5 md:flex">
    <x-logo />
    <nav class="mt-8 flex-1 space-y-1">
      @foreach ($menu as $item)
        @php $active = $item['route'] && request()->routeIs($item['match'] ?? $item['route']); @endphp
        @if ($item['route'])
          <a href="{{ route($item['route']) }}"
             class="flex items-center gap-3 rounded-full px-4 py-3 text-sm font-semibold transition {{ $active ? 'bg-brand-200 text-brand-700' : 'text-brand-900/80 hover:bg-brand-200/60' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
            {{ $item['label'] }}

        @if (! empty($item['badge']) && $unread)
            <span class="ml-auto rounded-full bg-brand-700 px-2 py-0.5 text-[10px] font-bold text-white">{{ $unread }}</span>
         @endif
          </a>
        @else
          <span class="flex cursor-not-allowed items-center gap-3 rounded-full px-4 py-3 text-sm font-semibold text-brand-900/40">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
            {{ $item['label'] }}
            <span class="ml-auto rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase">Bientôt</span>
          </span>
        @endif
      @endforeach
    </nav>

    <div class="mt-6 rounded-2xl bg-brand-400/15 p-4">
      <p class="truncate text-sm font-bold text-brand-900">{{ $user->name }}</p>
      <p class="truncate text-xs text-brand-900/60">{{ $user->phone }}</p>
      <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="btn-primary w-full !py-2 text-sm">Déconnexion</button>
      </form>
    </div>
  </aside>

  <div class="min-w-0 flex-1">
    {{-- Barre mobile --}}
    <header class="border-b border-brand-700/10 bg-white md:hidden">
      <div class="flex items-center justify-between px-4 py-3">
        <x-logo />
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button class="text-sm font-semibold text-brand-700">Quitter</button>
        </form>
      </div>
      <nav class="flex gap-2 overflow-x-auto px-4 pb-3">
        @foreach ($menu as $item)
          @if ($item['route'])
            <a href="{{ route($item['route']) }}"
               class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold {{ request()->routeIs($item['match'] ?? $item['route']) ? 'bg-brand-200 text-brand-700' : 'bg-brand-400/15 text-brand-900/80' }}">
              {{ $item['label'] }}

          @if (! empty($item['badge']) && $unread) ({{ $unread }}) @endif
            </a>
          @endif
        @endforeach
      </nav>
    </header>

    <main class="p-5 md:p-10">
      @if (session('success'))
        <div class="mb-6 rounded-2xl bg-brand-200 px-5 py-3 text-sm font-semibold text-brand-700">{{ session('success') }}</div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
</body>
</html>