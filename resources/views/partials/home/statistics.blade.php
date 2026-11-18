
<section id="statistik-desa" class="bg-white py-12">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <h2 class="section-title mb-6 text-3xl font-bold">{{ $homeCopy['statistics_heading'] }}</h2>

    @if ($statistics === [])
      <p class="rounded-2xl bg-gray-50 p-6 text-center text-sm text-gray-600">Data statistik desa belum tersedia.</p>
    @else
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
        @foreach ($statistics as $statistic)
          <div class="rounded-xl bg-green-50 p-4">
            <div class="mb-3 flex min-h-8 items-center justify-center text-center text-xs font-medium text-gray-600 sm:text-sm">
              {{ $statistic['label'] }}
            </div>
            <div class="flex items-center justify-center gap-3">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-green-700">
                <i class="{{ $statistic['icon'] ?: 'fas fa-chart-bar' }} text-xl" aria-hidden="true"></i>
              </div>
              <div class="text-2xl font-bold text-green-700 md:text-3xl">
                {{ $statistic['value'] }}@if ($statistic['unit']) <span class="text-sm font-semibold">{{ $statistic['unit'] }}</span>@endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
