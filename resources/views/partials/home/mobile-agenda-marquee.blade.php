@php
  $marqueeAgendas = $agendaGroups['ongoing']
      ->merge($agendaGroups['upcoming'])
      ->take(6);

  if ($marqueeAgendas->isEmpty()) {
      $marqueeAgendas = $agendaGroups['past']->take(6);
  }
@endphp

@if ($marqueeAgendas->isNotEmpty())
  <div class="border-b border-white/15 bg-gradient-to-r from-green-900 via-green-700 to-amber-600 text-white md:hidden">
    <div class="flex items-center gap-3 px-4 py-2">
      <a href="#agenda" class="flex shrink-0 items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-amber-100" aria-label="Lihat agenda desa">
        <i class="fas fa-calendar-days text-xs" aria-hidden="true"></i>
        Agenda
      </a>

      <div class="agenda-marquee min-w-0 flex-1">
        <div class="agenda-marquee-track">
          @foreach ([0, 1] as $copy)
            <div class="flex shrink-0 items-center gap-6 pr-6" @if ($copy === 1) aria-hidden="true" @endif>
              @foreach ($marqueeAgendas as $agenda)
                <a
                  href="#agenda"
                  class="flex shrink-0 items-center gap-2 text-xs text-white/90 transition hover:text-amber-200"
                  @if ($copy === 1) tabindex="-1" @endif
                >
                  <span class="font-semibold text-amber-200">{{ $agenda->dateLabel() }}</span>
                  <span class="max-w-56 truncate">{{ $agenda->title }}</span>
                </a>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endif
