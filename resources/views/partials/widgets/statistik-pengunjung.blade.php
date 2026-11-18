
<div class="rounded-2xl bg-white p-5 shadow">
  <h3 class="section-title mb-3 font-bold text-green-800">Statistik Pengunjung</h3>

  <dl class="grid grid-cols-3 gap-2 text-center">
    <div class="rounded-xl bg-green-50 p-3">
      <dt class="text-xs leading-4 text-gray-600">Hari ini</dt>
      <dd class="mt-1 text-xl font-bold text-green-700">{{ number_format($visitorStats['todayVisitors'] ?? 0, 0, ',', '.') }}</dd>
    </div>
    <div class="rounded-xl bg-green-50 p-3">
      <dt class="text-xs leading-4 text-gray-600">Aktif</dt>
      <dd class="mt-1 text-xl font-bold text-green-700">{{ number_format($visitorStats['activeVisitors'] ?? 0, 0, ',', '.') }}</dd>
    </div>
    <div class="rounded-xl bg-green-50 p-3">
      <dt class="text-xs leading-4 text-gray-600">Pembaca</dt>
      <dd class="mt-1 text-xl font-bold text-green-700">{{ number_format($visitorStats['totalReaders'] ?? 0, 0, ',', '.') }}</dd>
    </div>
  </dl>

  <p class="mt-3 text-xs leading-5 text-gray-500">
    Pengunjung dihitung dari sesi unik (hari ini dan {{ (int) config('session.lifetime', 120) }} menit terakhir); pembaca dari total kunjungan artikel.
  </p>
</div>
