<div class="rounded-2xl bg-gradient-to-br from-sky-500 to-blue-700 p-3 text-white shadow">
  <h3 class="mb-2 flex items-center gap-2 text-sm font-bold">
    <i class="fas fa-cloud-sun text-base" aria-hidden="true"></i>
    Prakiraan Cuaca
  </h3>

  <div class="flex items-center justify-between gap-3 rounded-xl bg-white/10 px-3 py-2">
    <div class="flex items-center gap-2">
      <i class="{{ $weather['current']['icon'] }} text-2xl text-amber-300" aria-hidden="true"></i>
      <span class="text-2xl font-bold leading-none">{{ $weather['current']['temperature'] }}°C</span>
    </div>
    <div class="min-w-0 text-right">
      <p class="text-xs font-medium text-blue-50">{{ $weather['current']['description'] }}</p>
      <p class="mt-0.5 text-[10px] text-blue-200">{{ $weather['location'] }}</p>
    </div>
  </div>

  @if ($weather['is_fallback'])
    <p class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-amber-400/20 px-2 py-0.5 text-[10px] font-semibold text-amber-100" title="Penyedia cuaca tidak dapat dihubungi; menampilkan data sementara.">
      <i class="fas fa-circle-info" aria-hidden="true"></i> Data sementara
    </p>
  @elseif ($weather['updated_at'])
    <p class="mt-1 text-[10px] text-blue-200">Diperbarui {{ $weather['updated_at'] }} WIB</p>
  @endif

  <div class="mt-2 grid grid-cols-4 gap-1.5 text-center text-[10px]">
    @foreach ($weather['forecast'] as $day)
      <div class="rounded-lg bg-white/15 px-1 py-1.5" title="{{ $day['description'] }}">
        <div class="text-blue-100">{{ $day['label'] }}</div>
        <i class="{{ $day['icon'] }} my-0.5 text-sm text-amber-300" aria-hidden="true"></i>
        <div class="font-semibold">{{ $day['temperature_max'] }}°</div>
      </div>
    @endforeach
  </div>
</div>
