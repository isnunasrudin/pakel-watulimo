@if (($visitorStats['todayVisitors'] ?? 0) > 0 || ($visitorStats['activeVisitors'] ?? 0) > 0 || ($visitorStats['totalReaders'] ?? 0) > 0)
<div class="rounded-2xl bg-white p-3 shadow">
  <h3 class="section-title mb-2 font-bold text-green-800">Statistik Pengunjung</h3>

  <div class="space-y-2">
    <div class="rounded-xl bg-green-50 p-3">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-[11px] font-medium text-gray-600">Hari Ini</p>
          <p class="mt-0.5 flex items-baseline gap-1">
            <span class="text-2xl font-bold leading-none text-green-700">{{ number_format($visitorStats['todayVisitors'] ?? 0, 0, ',', '.') }}</span>
            <span class="text-[10px] text-gray-500">pengunjung</span>
          </p>
        </div>
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-green-700">
          <i class="fas fa-users text-base" aria-hidden="true"></i>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-2">
      <div class="rounded-xl bg-green-50 p-2.5">
        <div class="mb-1 flex items-center gap-1.5 text-[11px] font-medium text-gray-600">
          <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-white text-green-700">
            <i class="fas fa-signal text-xs" aria-hidden="true"></i>
          </span>
          Aktif
        </div>
        <p class="text-lg font-bold leading-none text-green-700">{{ number_format($visitorStats['activeVisitors'] ?? 0, 0, ',', '.') }}</p>
      </div>

      <div class="rounded-xl bg-green-50 p-2.5">
        <div class="mb-1 flex items-center gap-1.5 text-[11px] font-medium text-gray-600">
          <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-white text-amber-600">
            <i class="fas fa-book-open text-xs" aria-hidden="true"></i>
          </span>
          Pembaca
        </div>
        <p class="text-lg font-bold leading-none text-green-700">{{ number_format($visitorStats['totalReaders'] ?? 0, 0, ',', '.') }}</p>
      </div>
    </div>
  </div>
</div>
@endif
