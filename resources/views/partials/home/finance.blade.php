
<section id="infografis-keuangan" class="bg-gray-50 py-12">
<div class="w-full px-4 md:px-8">
  <div class="rounded-2xl bg-white p-6 shadow">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="section-title text-2xl font-bold">{{ $homeCopy['finance_heading'] }}</h2>
        <p class="mt-1 text-sm text-gray-500">APBDes {{ $budget['year'] }} - Data Pelaksanaan Anggaran</p>
      </div>

      @if ($budget['availableYears'] !== [])
        <form method="GET" action="{{ route('home') }}#infografis-keuangan" class="flex items-center gap-2">
          <label for="apbdes-year" class="text-sm font-medium text-gray-600">Tahun</label>
          <select
            id="apbdes-year"
            name="tahun"
            aria-label="Pilih tahun APBDes"
            onchange="this.form.submit()"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100"
          >
            @foreach ($budget['availableYears'] as $year)
              <option value="{{ $year }}" @selected($year === $budget['year'])>{{ $year }}</option>
            @endforeach
          </select>
          <noscript>
            <button type="submit" class="rounded-lg bg-green-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-green-800">Tampilkan</button>
          </noscript>
        </form>
      @endif
    </div>

    @if (! $budget['hasData'])
      <p class="rounded-2xl bg-gray-50 p-6 text-sm text-gray-600">
        Data APBDes {{ $budget['year'] }} belum tersedia. Tambahkan item anggaran dari panel admin.
      </p>
    @else
      <div class="mb-6 grid gap-4 md:grid-cols-3">
        @foreach ($budget['sections'] as $section)
          <article class="rounded-xl border border-gray-100 bg-gray-50 p-4">
            <div class="flex items-center justify-between gap-2">
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $section['label'] }}</p>
              <span class="badge {{ $section['percentage'] >= 100 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                {{ number_format($section['percentage'], 1, ',', '.') }}%
              </span>
            </div>
            <p class="mt-2 text-xl font-bold text-green-800">Rp {{ number_format($section['realization'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500">dari Rp {{ number_format($section['budget'], 0, ',', '.') }}</p>
            <div
              class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200"
              role="progressbar"
              aria-label="Realisasi {{ $section['label'] }}"
              aria-valuemin="0"
              aria-valuemax="100"
              aria-valuenow="{{ min(100, max(0, $section['percentage'])) }}"
            >
              <div class="h-2 rounded-full bg-green-600" style="width: {{ min(100, max(0, $section['percentage'])) }}%"></div>
            </div>
          </article>
        @endforeach
      </div>

      <div class="mb-4 flex flex-wrap gap-2 text-sm md:hidden">
        <button class="inf-tab rounded-lg bg-green-700 px-4 py-2 font-semibold text-white" data-tab="pelaksanaan">Pelaksanaan</button>
        <button class="inf-tab rounded-lg bg-gray-100 px-4 py-2 font-semibold text-gray-700" data-tab="pendapatan">Pendapatan</button>
        <button class="inf-tab rounded-lg bg-gray-100 px-4 py-2 font-semibold text-gray-700" data-tab="pembelanjaan">Pembelanjaan</button>
      </div>

      <div class="grid gap-6 md:grid-cols-3">
        <div class="inf-panel" data-panel="pelaksanaan">
          <h3 class="mb-1 text-sm font-bold text-green-800">APBDes {{ $budget['year'] }} Pelaksanaan</h3>
          <p class="mb-2 text-xs text-gray-500">Realisasi vs Anggaran</p>
          <div class="relative" style="height:260px"><canvas id="chartPelaksanaan"></canvas></div>
        </div>

        <div class="inf-panel hidden md:block" data-panel="pendapatan">
          <h3 class="mb-1 text-sm font-bold text-green-800">APBDes {{ $budget['year'] }} Pendapatan</h3>
          <p class="mb-2 text-xs text-gray-500">Realisasi vs Anggaran</p>
          <div class="relative" style="height:260px"><canvas id="chartPendapatan"></canvas></div>
        </div>

        <div class="inf-panel hidden md:block" data-panel="pembelanjaan">
          <h3 class="mb-1 text-sm font-bold text-green-800">APBDes {{ $budget['year'] }} Pembelanjaan</h3>
          <p class="mb-2 text-xs text-gray-500">Realisasi vs Anggaran</p>
          <div class="relative" style="height:260px"><canvas id="chartBelanja"></canvas></div>
        </div>
      </div>
    @endif
  </div>
</div>
</section>

@if ($budget['hasData'])
  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const budget = @json($budget);
    const sections = Object.values(budget.sections);
    const pendapatan = budget.groups.pendapatan ?? [];
    const belanja = budget.groups.belanja ?? [];

    if (window.ChartDataLabels) Chart.register(ChartDataLabels);
    if (window.Chart) {
      Chart.defaults.color = document.documentElement.classList.contains('dark-mode') ? '#cbd5e1' : '#666';
    }

    const rupiah = (value) => 'Rp ' + Number(value).toLocaleString('id-ID');
    const axisTick = (value) => value >= 1e9 ? (value / 1e9).toFixed(1) + 'M' : value >= 1e6 ? (value / 1e6).toFixed(1) + 'Jt' : value;
    const commonOpts = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        datalabels: { display: false },
        legend: { position: 'top' },
        tooltip: {
          callbacks: {
            label: (context) => context.dataset.label + ': ' + rupiah(context.parsed.x ?? context.parsed.y),
          },
        },
      },
      scales: {
        x: { ticks: { callback: axisTick } },
        y: { ticks: { display: true } },
      },
    };

    const barData = (labels, budgetValues, realizationValues) => ({
      labels,
      datasets: [
        { label: 'Realisasi', data: realizationValues, backgroundColor: '#22c55e' },
        { label: 'Anggaran', data: budgetValues, backgroundColor: '#94a3b8' },
      ],
    });

    const chartConfigs = {
      pelaksanaan: {
        element: 'chartPelaksanaan',
        labels: sections.map((section) => section.label),
        budget: sections.map((section) => section.budget),
        realization: sections.map((section) => section.realization),
      },
      pendapatan: {
        element: 'chartPendapatan',
        labels: pendapatan.map((item) => item.label),
        budget: pendapatan.map((item) => item.budget),
        realization: pendapatan.map((item) => item.realization),
      },
      pembelanjaan: {
        element: 'chartBelanja',
        labels: belanja.map((item) => item.label),
        budget: belanja.map((item) => item.budget),
        realization: belanja.map((item) => item.realization),
      },
    };

    const chartsCreated = {};

    function createChart(name) {
      if (chartsCreated[name]) return;

      const config = chartConfigs[name];
      const canvas = document.getElementById(config.element);

      if (!canvas) return;

      chartsCreated[name] = true;
      new Chart(canvas, {
        type: 'bar',
        data: barData(config.labels, config.budget, config.realization),
        options: { ...commonOpts, indexAxis: 'y' },
      });
    }

    document.querySelectorAll('.inf-tab').forEach((button) => {
      button.addEventListener('click', () => {
        const tab = button.dataset.tab;

        document.querySelectorAll('.inf-tab').forEach((other) => {
          other.classList.remove('bg-green-700', 'text-white');
          other.classList.add('bg-gray-100', 'text-gray-700');
        });
        button.classList.remove('bg-gray-100', 'text-gray-700');
        button.classList.add('bg-green-700', 'text-white');

        document.querySelectorAll('.inf-panel').forEach((panel) => {
          panel.classList.add('hidden');
          panel.classList.add('md:block');
        });
        document.querySelector('.inf-panel[data-panel="' + tab + '"]')?.classList.remove('hidden');

        createChart(tab);
      });
    });

    if (window.innerWidth >= 768) {
      ['pelaksanaan', 'pendapatan', 'pembelanjaan'].forEach(createChart);
    } else {
      createChart('pelaksanaan');
    }
  });
  </script>
  @endpush
@endif
