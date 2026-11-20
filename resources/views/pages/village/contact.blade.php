@extends('layouts.app')

@section('title', 'Kontak')
@section('meta_description', 'Informasi kontak dan lokasi kantor '.$villageSettings->village_name.', Kecamatan '.$villageSettings->district_name.'.')
@section('canonical', route('pages.contact'))

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Kontak</span>
    </nav>

    <header class="mb-8">
      <span class="badge mb-3 bg-amber-100 text-amber-700">Hubungi Kami</span>
      <h1 class="text-3xl font-bold text-green-800 md:text-4xl">Kontak {{ $villageSettings->village_name }}</h1>
      <p class="mt-2 max-w-2xl text-gray-600">Hubungi kantor desa melalui informasi kontak berikut.</p>
    </header>

    <div class="grid gap-6 lg:grid-cols-2">
      <section class="rounded-2xl bg-white p-6 shadow-sm md:p-8" aria-labelledby="contact-details-title">
        <h2 id="contact-details-title" class="section-title mb-6 text-2xl font-bold text-gray-900">Informasi Kantor</h2>
        <dl class="space-y-5">
          @if ($villageSettings->address)
            <div class="flex gap-4">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-700"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
              <div><dt class="font-semibold text-gray-900">Alamat</dt><dd class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $villageSettings->address }}</dd></div>
            </div>
          @endif
          @if ($villageSettings->phone)
            <div class="flex gap-4">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-700"><i class="fas fa-phone" aria-hidden="true"></i></span>
              <div><dt class="font-semibold text-gray-900">Telepon</dt><dd class="mt-1 text-sm text-gray-600">{{ $villageSettings->phone }}</dd></div>
            </div>
          @endif
          @if ($villageSettings->email)
            <div class="flex gap-4">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-700"><i class="fas fa-envelope" aria-hidden="true"></i></span>
              <div><dt class="font-semibold text-gray-900">Email</dt><dd class="mt-1 text-sm"><a href="mailto:{{ $villageSettings->email }}" class="text-green-700 hover:underline">{{ $villageSettings->email }}</a></dd></div>
            </div>
          @endif
          @if ($villageSettings->safeWhatsappUrl())
            <div class="flex gap-4">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-700"><i class="fab fa-whatsapp" aria-hidden="true"></i></span>
              <div><dt class="font-semibold text-gray-900">WhatsApp</dt><dd class="mt-1 text-sm"><a href="{{ $villageSettings->safeWhatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="text-green-700 hover:underline">Kirim pesan</a></dd></div>
            </div>
          @endif
          @if ($villageSettings->office_hours)
            <div class="flex gap-4">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-700"><i class="fas fa-clock" aria-hidden="true"></i></span>
              <div><dt class="font-semibold text-gray-900">Jam pelayanan</dt><dd class="mt-1 text-sm text-gray-600">{{ $villageSettings->office_hours }}</dd></div>
            </div>
          @endif
          @if (! $villageSettings->address && ! $villageSettings->phone && ! $villageSettings->email && ! $villageSettings->safeWhatsappUrl() && ! $villageSettings->office_hours)
            <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">Informasi kontak belum tersedia.</p>
          @endif
        </dl>

        @if ($socialLinks->isNotEmpty())
          <div class="mt-8 border-t border-gray-100 pt-6">
            <h3 class="mb-3 font-semibold text-gray-900">Ikuti media sosial</h3>
            <div class="flex flex-wrap gap-3">
              @foreach ($socialLinks as $socialLink)
                @if ($socialLink->safeUrl())
                  <a href="{{ $socialLink->safeUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-800 transition hover:bg-green-100">
                    <i class="{{ $socialLink->icon ?: 'fas fa-globe' }}" aria-hidden="true"></i>{{ $socialLink->platform }}
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        @endif
      </section>

      <section class="overflow-hidden rounded-2xl bg-white p-3 shadow-sm" aria-label="Peta lokasi kantor desa">
        @if ($villageSettings->safeMapEmbedUrl())
          <iframe src="{{ $villageSettings->safeMapEmbedUrl() }}" title="Peta lokasi {{ $villageSettings->village_name }}" class="min-h-80 h-full w-full rounded-xl border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        @elseif ($villageSettings->safeMapsUrl())
          <div class="flex min-h-80 flex-col items-center justify-center gap-4 rounded-xl bg-green-50 p-8 text-center">
            <i class="fas fa-map-location-dot text-4xl text-green-700" aria-hidden="true"></i>
            <p class="text-sm text-gray-600">Lihat lokasi kantor desa pada peta.</p>
            <a href="{{ $villageSettings->safeMapsUrl() }}" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-800">Buka Google Maps</a>
          </div>
        @else
          <div class="flex min-h-80 items-center justify-center rounded-xl bg-green-50 p-8 text-center text-sm text-gray-600">Peta lokasi kantor desa belum tersedia.</div>
        @endif
      </section>
    </div>
  </div>
</main>

@include('partials.home.footer')
@endsection
