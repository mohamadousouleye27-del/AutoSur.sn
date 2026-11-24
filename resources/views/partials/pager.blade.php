<div class="mt-4 flex items-center justify-between text-sm">
  <span class="text-brand-900/60">Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} · {{ $paginator->total() }} résultat(s)</span>
  <div class="flex gap-2">
    @if ($paginator->previousPageUrl())<a href="{{ $paginator->previousPageUrl() }}" class="btn-accent !py-2 text-sm">← Précédent</a>@endif
    @if ($paginator->nextPageUrl())<a href="{{ $paginator->nextPageUrl() }}" class="btn-accent !py-2 text-sm">Suivant →</a>@endif
  </div>
</div>