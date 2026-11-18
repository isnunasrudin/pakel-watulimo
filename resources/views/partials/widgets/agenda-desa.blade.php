@php
  $agendaTabs = [
      'lalu' => ['label' => 'Lalu', 'title' => 'Agenda yang sudah berlalu'],
      'sedang' => ['label' => 'Sedang', 'title' => 'Agenda yang sedang berlangsung'],
      'akan' => ['label' => 'Mendatang', 'title' => 'Agenda yang akan datang'],
  ];

  $activeAgendaTab = $agendaGroups['ongoing']->isNotEmpty()
      ? 'sedang'
      : ($agendaGroups['upcoming']->isNotEmpty() ? 'akan' : 'lalu');

  $agendaPanels = [
      'lalu' => [
          'items' => $agendaGroups['past'],
          'card' => 'bg-gray-50',
          'accent' => 'bg-gray-400',
          'date' => 'text-gray-500',
          'empty' => 'Belum ada agenda yang sudah berlalu.',
      ],
      'sedang' => [
          'items' => $agendaGroups['ongoing'],
          'card' => 'bg-green-50',
          'accent' => 'bg-green-600',
          'date' => 'text-green-700',
          'empty' => 'Tidak ada agenda yang sedang berlangsung.',
      ],
      'akan' => [
          'items' => $agendaGroups['upcoming'],
          'card' => 'bg-amber-50',
          'accent' => 'bg-amber-500',
          'date' => 'text-amber-600',
          'empty' => 'Belum ada agenda mendatang.',
      ],
  ];
@endphp

<div id="agenda" class="rounded-2xl bg-white p-4 shadow">
  <div class="mb-3 flex items-center justify-between gap-3">
    <h3 class="section-title font-bold text-green-800">Agenda Desa</h3>
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-700" aria-hidden="true">
      <i class="fas fa-calendar-days text-sm"></i>
    </span>
  </div>

  <div class="mb-3 grid grid-cols-3 gap-1 rounded-xl bg-gray-100 p-1" role="tablist" aria-label="Filter agenda desa">
    @foreach ($agendaTabs as $tab => $agendaTab)
      <button
        id="agenda-tab-{{ $tab }}"
        type="button"
        role="tab"
        aria-selected="{{ $tab === $activeAgendaTab ? 'true' : 'false' }}"
        aria-controls="agenda-panel-{{ $tab }}"
        title="{{ $agendaTab['title'] }}"
        class="agenda-tab rounded-lg px-2 py-1.5 text-xs font-semibold transition {{ $tab === $activeAgendaTab ? 'bg-green-700 text-white shadow-sm' : 'text-gray-600 hover:bg-white' }}"
        data-tab="{{ $tab }}"
      >
        {{ $agendaTab['label'] }}
      </button>
    @endforeach
  </div>

  @foreach ($agendaPanels as $tab => $panel)
    <div
      id="agenda-panel-{{ $tab }}"
      class="agenda-panel {{ $tab === $activeAgendaTab ? '' : 'hidden' }}"
      role="tabpanel"
      aria-labelledby="agenda-tab-{{ $tab }}"
      data-panel="{{ $tab }}"
    >
      <div class="space-y-2">
        @forelse ($panel['items'] as $agenda)
          <article class="relative overflow-hidden rounded-xl border border-gray-100 p-3 pl-4 {{ $panel['card'] }}">
            <span class="absolute inset-y-2 left-0 w-1 rounded-full {{ $panel['accent'] }}" aria-hidden="true"></span>

            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <p class="text-xs font-semibold {{ $panel['date'] }}">{{ $agenda->dateLabel() }}</p>
                <h4 class="mt-1 line-clamp-2 text-sm font-bold leading-snug text-gray-900" title="{{ $agenda->title }}">{{ $agenda->title }}</h4>
              </div>

              @if ($tab === 'sedang')
                <span class="badge shrink-0 bg-green-100 text-green-700">Berlangsung</span>
              @endif
            </div>

            @if ($agenda->location)
              <p class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                <i class="fas fa-map-marker-alt text-green-700" aria-hidden="true"></i>
                <span class="line-clamp-1">{{ $agenda->location }}</span>
              </p>
            @endif
          </article>
        @empty
          <p class="rounded-xl bg-gray-50 p-3 text-center text-xs leading-5 text-gray-500">{{ $panel['empty'] }}</p>
        @endforelse
      </div>
    </div>
  @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.agenda-tab');
  const panels = document.querySelectorAll('.agenda-panel');

  if (!tabs.length || !panels.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const selectedTab = tab.dataset.tab;

      tabs.forEach((item) => {
        const isActive = item === tab;

        item.classList.toggle('bg-green-700', isActive);
        item.classList.toggle('text-white', isActive);
        item.classList.toggle('shadow-sm', isActive);
        item.classList.toggle('text-gray-600', !isActive);
        item.classList.toggle('hover:bg-white', !isActive);
        item.setAttribute('aria-selected', String(isActive));
      });

      panels.forEach((panel) => {
        panel.classList.toggle('hidden', panel.dataset.panel !== selectedTab);
      });
    });
  });
});
</script>
@endpush
