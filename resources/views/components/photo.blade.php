@props(['src', 'alt' => ''])

@php $isUrl = str_starts_with($src, 'http'); @endphp

@if ($isUrl || file_exists(public_path('images/' . $src)))
  <img src="{{ $isUrl ? $src : asset('images/' . $src) }}" alt="{{ $alt }}" loading="lazy" {{ $attributes }}>
@else
  <div {{ $attributes->class(['flex items-center justify-center bg-brand-400/20 p-2 text-center text-xs font-semibold text-brand-700/60']) }}>
    Ajouter : public/images/{{ $src }}
  </div>
@endif