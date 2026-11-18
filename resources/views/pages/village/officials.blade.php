@extends('layouts.app')

@section('title', 'Pemerintahan '.$villageSettings->village_name.' | Perangkat Desa')
@section('meta_description', 'Susunan perangkat desa dan pemerintahan '.$villageSettings->village_name.', Kecamatan '.$villageSettings->district_name.'.')
@section('canonical', route('pages.officials'))
@section('og_title', 'Pemerintahan '.$villageSettings->village_name)

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Pemerintahan</span>
    </nav>

    <header class="mb-8">
      <span class="badge mb-3 bg-amber-100 text-amber-700">Pemerintahan Desa</span>
      <h1 class="text-3xl font-bold text-green-800 md:text-4xl">Perangkat {{ $villageSettings->village_name }}</h1>
      <p class="mt-2 max-w-2xl text-gray-600">Kenali perangkat desa dan susunan pemerintahan {{ $villageSettings->village_name }}.</p>
    </header>

    @if ($officials->isEmpty())
      <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow-sm">Data perangkat desa belum tersedia.</p>
    @else
      <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($officials as $official)
          <article class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <img src="{{ $official->photoUrl() }}" alt="Foto {{ $official->name }}" class="block w-full h-auto" style="background-color: {{ $official->backgroundColor() }}" loading="lazy">
            <div class="p-5">
              <h2 class="text-lg font-bold text-gray-900">{{ $official->name }}</h2>
              <p class="mt-1 text-sm font-medium text-green-700">{{ $official->position }}</p>
              @if ($official->phone)
                <div class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-600">
                  <p><i class="fas fa-phone mr-2 text-green-700" aria-hidden="true"></i>{{ $official->phone }}</p>
                </div>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif

    <section id="pilar-desa" class="mt-12" aria-labelledby="pillars-title">
      <header class="mb-8">
        <span class="badge mb-3 bg-green-100 text-green-800">Mitra Pemerintahan</span>
        <h2 id="pillars-title" class="text-3xl font-bold text-green-800 md:text-4xl">Pilar Desa</h2>
        <p class="mt-2 max-w-2xl text-gray-600">Babinsa, bidan desa, dan mitra pemerintahan yang mendukung {{ $villageSettings->village_name }}.</p>
      </header>

      @if ($pillars->isEmpty())
        <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow-sm">Data pilar desa belum tersedia.</p>
      @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          @foreach ($pillars as $pillar)
            <article class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
              <img src="{{ $pillar->photoUrl() }}" alt="Foto {{ $pillar->name }}" class="block w-full h-auto" style="background-color: {{ $pillar->backgroundColor() }}" loading="lazy">
              <div class="p-5">
                <h3 class="text-lg font-bold text-gray-900">{{ $pillar->name }}</h3>
                <p class="mt-1 text-sm font-medium text-green-700">{{ $pillar->position }}</p>
                @if ($pillar->phone)
                  <div class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-600">
                    <p><i class="fas fa-phone mr-2 text-green-700" aria-hidden="true"></i>{{ $pillar->phone }}</p>
                  </div>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </section>
  </div>
</main>

@include('partials.home.footer')
@endsection
