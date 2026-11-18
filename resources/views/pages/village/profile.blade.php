@extends('layouts.app')

@section('title', $villageSettings->village_name.' | Profil Desa')
@section('meta_description', $villageSettings->description ?: 'Profil '.$villageSettings->village_name.', Kecamatan '.$villageSettings->district_name.', Kabupaten '.$villageSettings->regency_name.'.')
@section('canonical', route('pages.profile'))
@section('og_title', $villageSettings->village_name.' | Profil Desa')

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl space-y-10 px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Profil Desa</span>
    </nav>

    <header class="rounded-2xl bg-white p-6 shadow-sm md:p-10">
      <span class="badge mb-3 bg-amber-100 text-amber-700">Tentang Kami</span>
      <h1 class="text-3xl font-bold text-green-800 md:text-4xl">{{ $villageSettings->village_name }}</h1>
      <p class="mt-2 text-gray-600">
        {{ collect([$villageSettings->district_name, $villageSettings->regency_name, $villageSettings->province_name])->filter()->implode(', ') }}
      </p>
      @if ($villageSettings->description)
        <div class="mt-6 max-w-4xl whitespace-pre-line leading-8 text-gray-700">{{ $villageSettings->description }}</div>
      @else
        <p class="mt-6 max-w-4xl leading-8 text-gray-600">Informasi profil desa belum ditambahkan. Silakan lengkapi dari panel admin.</p>
      @endif
    </header>

    <section id="statistik-desa" aria-labelledby="village-statistics-title">
      <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
          <span class="badge mb-3 bg-green-100 text-green-800">Data Wilayah</span>
          <h2 id="village-statistics-title" class="section-title text-2xl font-bold text-gray-900">Statistik Desa</h2>
        </div>
        <a href="{{ route('home') }}#statistik-desa" class="text-sm font-semibold text-green-700 hover:underline">Lihat di beranda</a>
      </div>

      @if ($statistics->isEmpty())
        <p class="rounded-2xl bg-white p-6 text-sm text-gray-600 shadow-sm">Data statistik belum tersedia.</p>
      @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
          @foreach ($statistics as $statistic)
            <article class="rounded-xl bg-white p-4 shadow-sm">
              <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-700">
                <i class="{{ $statistic->icon ?: 'fas fa-chart-bar' }} text-xl" aria-hidden="true"></i>
              </div>
              <p class="text-sm text-gray-600">{{ $statistic->label }}</p>
              <p class="mt-1 text-2xl font-bold text-green-700">{{ $statistic->value }} <span class="text-sm font-semibold">{{ $statistic->unit }}</span></p>
            </article>
          @endforeach
        </div>
      @endif
    </section>
  </div>
</main>

@include('partials.home.footer')
@endsection
