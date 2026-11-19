@if ($programLinks !== [])

<div class="rounded-2xl bg-white p-4 shadow">
  <div class="mb-3 flex items-center justify-between gap-3">
    <h3 class="section-title font-bold text-green-800">Sinergi Program</h3>
    <a href="{{ route('pages.services') }}" class="text-xs font-semibold text-green-700 hover:underline">Semua tautan</a>
  </div>

    <div class="grid grid-cols-1 gap-3">
      @foreach ($programLinks as $programLink)
        @php($imageUrl = $programLink['image_url'] ?? null)
        @if (filled($imageUrl))
          @if (filled($programLink['safe_url'] ?? null))
            <a href="{{ $programLink['safe_url'] }}" class="group block overflow-hidden rounded-xl bg-gray-100 transition hover:-translate-y-0.5" style="background-color: {{ $programLink['background_color'] }}" aria-label="{{ $programLink['title'] }}">
              <img src="{{ $imageUrl }}" alt="{{ $programLink['title'] }}" class="block h-auto w-full transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
            </a>
          @else
            <div class="overflow-hidden rounded-xl bg-gray-100" style="background-color: {{ $programLink['background_color'] }}">
              <img src="{{ $imageUrl }}" alt="{{ $programLink['title'] }}" class="block h-auto w-full" loading="lazy" decoding="async">
            </div>
          @endif
        @endif
      @endforeach
    </div>
</div>
@endif
