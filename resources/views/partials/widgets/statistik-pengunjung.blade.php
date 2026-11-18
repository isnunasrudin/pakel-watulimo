<div class="rounded-2xl bg-white p-4 shadow">
  <h3 class="section-title mb-3 font-bold text-green-800">Statistik Pengunjung</h3>

  <div class="space-y-3">
    <div class="rounded-xl bg-green-50 p-4">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-xs font-medium text-gray-600">Hari Ini</p>
          <p class="mt-1 flex items-baseline gap-1.5">
            <span class="text-3xl font-bold text-green-700">{{ number_format($visitorStats['todayVisitors'] ?? 0, 0, ',', '.') }}</span>
            <span class="text-xs text-gray-500">pengunjung</span>
          </p>
        </div>
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-green-700">
          <i class="fas fa-users text-xl" aria-hidden="true"></i>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div class="rounded-xl bg-green-50 p-3">
        <div class="mb-2 flex items-center gap-2 text-xs font-medium text-gray-600">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-green-700">
            <i class="fas fa-signal text-sm" aria-hidden="true"></i>
          </span>
          <p>Aktif</p>
        </div>
        <p class="text-xl font-bold text-green-700">{{ number_format($visitorStats['activeVisitors'] ?? 0, 0, ',', '.') }}</p>
      </div>

      <div class="rounded-xl bg-green-50 p-3">
        <div class="mb-2 flex items-center gap-2 text-xs font-medium text-gray-600">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-amber-600">
            <i class="fas fa-book-open text-sm" aria-hidden="true"></i>
          </span>
          <p>Pembaca</p>
        </div>
        <p class="text-xl font-bold text-green-700">{{ number_format($visitorStats['totalReaders'] ?? 0, 0, ',', '.') }}</p>
      </div>
    </div>
  </div>
</div>
