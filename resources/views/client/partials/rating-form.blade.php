<details class="rounded-2xl border border-brand-700/20 px-4 py-2">
  <summary class="cursor-pointer text-sm font-semibold text-brand-700">{{ $label }}</summary>

  <form method="POST" action="{{ $action }}" class="mt-3 space-y-3">
    @csrf
    <div class="flex flex-wrap gap-2">
      @foreach (range(1, 5) as $n)
        <label class="cursor-pointer">
          <input type="radio" name="score" value="{{ $n }}" class="peer sr-only" required>
          <span class="block rounded-full border border-brand-700/20 px-4 py-1.5 text-sm font-semibold peer-checked:border-brand-700 peer-checked:bg-brand-200">{{ $n }} ★</span>
        </label>
      @endforeach
    </div>

    <textarea name="comment" rows="2" maxlength="500" placeholder="Votre commentaire (facultatif)"
              class="w-full rounded-2xl border border-brand-700/20 bg-white px-5 py-3 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-400/40">{{ old('comment') }}</textarea>

    <button class="btn-primary !py-2 text-sm">Envoyer mon avis</button>
  </form>
</details>