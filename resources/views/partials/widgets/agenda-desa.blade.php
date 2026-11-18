
@php
  $agendaTabs = [
      'lalu' => 'Sudah Berlalu',
      'sedang' => 'Sedang',
      'akan' => 'Akan Datang',
  ];

  $activeAgendaTab = $agendaGroups['ongoing']->isNotEmpty()
      ? 'sedang'
      : ($agendaGroups['upcoming']->isNotEmpty() ? 'akan' : 'lalu');

  $agendaPanels = [
      'lalu' => [
          'items' => $agendaGroups['past'],
          'item' => 'border-gray-400 bg-gray-50',
          'date' => 'text-gray-500',
          'empty' => 'Belum ada agenda yang sudah berlalu.',
      ],
      'sedang' => [
          'items' => $agendaGroups['ongoing'],
          'item' => 'border-green-500 bg-green-50',
          'date' => 'text-green-600',
          'empty' => 'Tidak ada agenda yang sedang berlangsung.',
      ],
      'akan' => [
          'items' => $agendaGroups['upcoming'],
          'item' => 'border-amber-500 bg-amber-50',
          'date' => 'text-amber-600',
          'empty' => 'Belum ada agenda mendatang.',
      ],
  ];
@endphp

<div id="agenda" class="bg-white rounded-2xl shadow p-4">
  <h3 class="font-bold text-green-800 mb-3 section-title">Agenda Desa</h3>

  <div class="flex gap-1 mb-3 text-xs">
    @foreach ($agendaTabs as $tab => $label)
      <button
        class="agenda-tab flex-1 rounded-lg py-1.5 font-semibold {{ $tab === $activeAgendaTab ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-600' }}"
        data-tab="{{ $tab }}"
        type="button"
      >
        {{ $label }}
      </button>
    @endforeach
  </div>

  @foreach ($agendaPanels as $tab => $panel)
    <div class="agenda-panel {{ $tab === $activeAgendaTab ? '' : 'hidden' }}" data-panel="{{ $tab }}">
      <div class="space-y-2">
        @forelse ($panel['items'] as $agenda)
          <div class="rounded-lg border-l-2 p-2 {{ $panel['item'] }}">
            <div class="text-xs {{ $panel['date'] }}">{{ $agenda->dateLabel() }}</div>
            <div class="text-xs font-semibold">{{ $agenda->title }}</div>
            @if ($agenda->location)
              <div class="mt-1 text-xs text-gray-500">
                <i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i>{{ $agenda->location }}
              </div>
            @endif
            @if ($tab === 'sedang')
              <span class="badge mt-1 bg-green-100 text-green-700">Berlangsung</span>
            @endif
          </div>
        @empty
          <p class="rounded-lg bg-gray-50 p-3 text-xs text-gray-500">{{ $panel['empty'] }}</p>
        @endforelse
      </div>
    </div>
  @endforeach
</div>

@push('scripts')
<script>
(function(){
  const tabs = document.querySelectorAll('.agenda-tab');
  if (!tabs.length) return;

  tabs.forEach((button) => {
    button.addEventListener('click', () => {
      const tab = button.dataset.tab;

      document.querySelectorAll('.agenda-tab').forEach((other) => {
        other.classList.remove('bg-green-700', 'text-white');
        other.classList.add('bg-gray-100', 'text-gray-600');
      });
      button.classList.remove('bg-gray-100', 'text-gray-600');
      button.classList.add('bg-green-700', 'text-white');

      document.querySelectorAll('.agenda-panel').forEach((panel) => {
        panel.classList.add('hidden');
      });
      document.querySelector('.agenda-panel[data-panel="' + tab + '"]')?.classList.remove('hidden');
    });
  });
})();
</script>
@endpush
