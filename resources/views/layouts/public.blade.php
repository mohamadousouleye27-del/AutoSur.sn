<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'AutoSur')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
  <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
    <div class="flex items-center gap-10">
      <x-logo />
      <nav class="hidden gap-8 text-sm font-medium md:flex">
        <a href="{{ route('home') }}#services">Services</a>
        <a href="{{ route('home') }}#partenaires">Partenaires</a>
        <a href="{{ route('home') }}#contact">Contact</a>
      </nav>
    </div>
    <div class="flex items-center gap-3">
  @auth
    <a href="{{ route(auth()->user()->homeRoute()) }}" class="text-sm font-medium">Mon espace</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn-primary !py-2 text-sm">Déconnexion</button>
    </form>
  @else
    <a href="{{ route('login') }}" class="text-sm font-medium">Connexion</a>
    <a href="{{ route('register') }}" class="btn-primary !py-2 text-sm">Inscription</a>
  @endauth
</div>
  </header>

  <main>@yield('content')</main>
</body>
</html>