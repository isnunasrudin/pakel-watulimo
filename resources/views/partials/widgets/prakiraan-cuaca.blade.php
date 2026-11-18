
<div class="bg-gradient-to-br from-sky-500 to-blue-700 text-white rounded-2xl shadow p-5">
  <h3 class="font-bold mb-3 flex items-center gap-2"><i class="fas fa-cloud-sun text-xl" aria-hidden="true"></i> Prakiraan Cuaca</h3>
  <div class="text-center py-2">
    <div class="weather-icon mb-1"><i class="{{ $weather['current']['icon'] }} text-xl" aria-hidden="true"></i></div>
    <div class="text-4xl font-bold">{{ $weather['current']['temperature'] }}°C</div>
    <div class="text-sm text-blue-100">{{ $weather['current']['description'] }}</div>
    <div class="text-xs text-blue-200 mt-1">{{ $weather['location'] }}</div>
    @if ($weather['is_fallback'])
      <div class="mt-2 inline-flex items-center gap-1 rounded-full bg-amber-400/20 px-2 py-1 text-[10px] font-semibold text-amber-100" title="Penyedia cuaca tidak dapat dihubungi; menampilkan data sementara.">
        <i class="fas fa-circle-info" aria-hidden="true"></i> Data sementara
      </div>
    @elseif ($weather['updated_at'])
      <div class="mt-1 text-[10px] text-blue-200">Diperbarui {{ $weather['updated_at'] }} WIB</div>
    @endif
  </div>
  <div class="mt-4 grid grid-cols-4 gap-2 text-center text-xs">
    @foreach ($weather['forecast'] as $day)
      <div class="rounded-lg bg-white/15 p-2" title="{{ $day['description'] }}">
        <div>{{ $day['label'] }}</div>
        <i class="{{ $day['icon'] }} my-1 text-lg text-amber-300" aria-hidden="true"></i>
        <div>{{ $day['temperature_max'] }}°</div>
      </div>
    @endforeach
  </div>
</div>
