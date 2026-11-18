@php
  $logoUrl = $villageSettings->iconUrl();
@endphp


<footer id="kontak" class="bg-green-900 pt-12 text-gray-200">
  <div class="mx-auto grid max-w-7xl gap-8 px-4 pb-8 md:grid-cols-2 lg:grid-cols-4">
    <div>
      <div class="mb-3 flex items-center gap-3">
        @if ($logoUrl)
          <img src="{{ $logoUrl }}" alt="Logo {{ $villageSettings->village_name }}" class="h-10 w-10 rounded-full object-contain">
        @endif
        <h4 class="font-bold text-white">{{ $villageSettings->village_name }}</h4>
      </div>
      <p class="text-sm text-green-100">
        {{ collect([$villageSettings->district_name, $villageSettings->regency_name, $villageSettings->province_name])->filter()->implode(', ') }}
      </p>
      @if ($socialLinks !== [])
        <div class="mt-4 flex flex-wrap gap-3">
          @foreach ($socialLinks as $socialLink)
            @if ($socialLink['safe_url'])
              <a href="{{ $socialLink['safe_url'] }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 transition hover:bg-amber-500" aria-label="{{ $socialLink['platform'] }}">
                <i class="{{ $socialLink['icon'] ?: 'fas fa-globe' }} text-xl" aria-hidden="true"></i>
              </a>
            @endif
          @endforeach
        </div>
      @endif
    </div>

    <div>
      <h4 class="mb-3 font-bold text-white">Layanan &amp; Tautan Cepat</h4>
      <ul class="space-y-2 text-sm">
        @forelse (array_slice($serviceLinks, 0, 5) as $serviceLink)
          @if ($serviceLink['safe_url'])
            <li><a href="{{ $serviceLink['safe_url'] }}" class="transition hover:text-amber-400">{{ $serviceLink['title'] }}</a></li>
          @endif
        @empty
          <li><a href="{{ route('pages.services') }}" class="transition hover:text-amber-400">Lihat halaman layanan</a></li>
        @endforelse
        <li><a href="{{ route('articles.index') }}" class="transition hover:text-amber-400">Berita Desa</a></li>
      </ul>
    </div>

    <div>
      <h4 class="mb-3 font-bold text-white">Kontak</h4>
      <ul class="space-y-2 text-sm">
        @if ($villageSettings->address)
          <li class="flex items-start gap-2"><i class="fas fa-map-marker-alt mt-1 text-base" aria-hidden="true"></i><span>{{ $villageSettings->address }}</span></li>
        @endif
        @if ($villageSettings->phone)
          <li class="flex items-start gap-2"><i class="fas fa-phone mt-1 text-base" aria-hidden="true"></i><span>{{ $villageSettings->phone }}</span></li>
        @endif
        @if ($villageSettings->email)
          <li class="flex items-start gap-2"><i class="fas fa-envelope mt-1 text-base" aria-hidden="true"></i><a href="mailto:{{ $villageSettings->email }}" class="hover:text-amber-400">{{ $villageSettings->email }}</a></li>
        @endif
        @if ($villageSettings->safeWhatsappUrl())
          <li class="flex items-start gap-2"><i class="fab fa-whatsapp mt-1 text-base" aria-hidden="true"></i><a href="{{ $villageSettings->safeWhatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="hover:text-amber-400">WhatsApp</a></li>
        @endif
        @if ($villageSettings->office_hours)
          <li class="flex items-start gap-2"><i class="fas fa-clock mt-1 text-base" aria-hidden="true"></i><span>{{ $villageSettings->office_hours }}</span></li>
        @endif
        @if (! $villageSettings->address && ! $villageSettings->phone && ! $villageSettings->email && ! $villageSettings->safeWhatsappUrl() && ! $villageSettings->office_hours)
          <li>Informasi kontak belum tersedia.</li>
        @endif
      </ul>
    </div>

    <div>
      <h4 class="mb-3 font-bold text-white">Kantor Desa</h4>
      @if ($villageSettings->safeMapEmbedUrl())
        <div class="overflow-hidden rounded-lg">
          <iframe src="{{ $villageSettings->safeMapEmbedUrl() }}" title="Peta lokasi {{ $villageSettings->village_name }}" class="h-44 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
      @elseif ($villageSettings->safeMapsUrl())
        <a href="{{ $villageSettings->safeMapsUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-4 py-3 text-sm transition hover:bg-white/20">
          <i class="fas fa-map-location-dot" aria-hidden="true"></i>Buka lokasi di peta
        </a>
      @else
        <p class="text-sm text-green-100">Lokasi kantor desa belum ditambahkan.</p>
      @endif
    </div>
  </div>

  <div class="border-t border-green-800 py-4 text-center text-xs text-green-200">
    &copy; {{ now()->year }} {{ $villageSettings->village_name }}. All rights reserved.
  </div>
</footer>
