@php
  $heroImage = $villageSettings->heroUrl() ?: $albums->first()?->coverUrl();
  $heroLogo = $villageSettings->iconUrl();
@endphp


<div id="beranda">
  @include('partials.home.mobile-agenda-marquee')
<section class="hero-bg hidden min-h-[80vh] items-center text-white md:flex" @if ($heroImage) style="background-image: var(--hero-overlay), url('{{ $heroImage }}')" @endif>
<div class="max-w-7xl mx-auto px-4 py-24 md:py-32 w-full">
  <div class="grid items-center gap-8 {{ $heroLogo ? 'md:grid-cols-[minmax(0,1.2fr)_minmax(260px,.8fr)] lg:gap-12' : '' }}">
    <div class="max-w-3xl">
    <span class="badge bg-amber-500 text-white mb-6">{{ $homeCopy['badge'] }}</span>
    <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">{{ $homeCopy['title'] }}<br><span class="text-amber-400">{{ $homeCopy['highlight'] }}</span></h1>
    <p class="text-xl md:text-2xl text-gray-200 mb-10 max-w-2xl">{{ $homeCopy['description'] }}</p>
    <div class="flex flex-wrap gap-4">
      <a href="{{ $homeCopy['primary_url'] }}" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-4 rounded-lg font-semibold text-lg"><i class="fas fa-newspaper mr-2 text-base" aria-hidden="true"></i>{{ $homeCopy['primary_label'] }}</a>
      <a href="{{ $homeCopy['secondary_url'] }}" class="bg-white/20 backdrop-blur hover:bg-white/30 text-white px-8 py-4 rounded-lg font-semibold text-lg border border-white/40"><i class="fas fa-info-circle mr-2 text-base" aria-hidden="true"></i>{{ $homeCopy['secondary_label'] }}</a>
    </div>
    </div>
    @if ($heroLogo)
      <div class="hidden min-h-[320px] items-center justify-center md:flex">
        <img src="{{ $heroLogo }}" alt="Logo {{ $villageSettings->village_name }}" class="h-auto w-full max-w-sm object-contain drop-shadow-2xl" fetchpriority="high">
      </div>
    @endif
  </div>
</div>
</section>
</div>
