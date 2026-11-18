@extends('layouts.app')

@section('title', 'Layanan '.$villageSettings->village_name.' | Website Resmi')
@section('meta_description', 'Layanan publik dan tautan penting '.$villageSettings->village_name.', Kecamatan '.$villageSettings->district_name.'.')
@section('canonical', route('pages.services'))
@section('og_title', 'Layanan '.$villageSettings->village_name)

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Layanan</span>
    </nav>

    <header class="mb-8">
      <span class="badge mb-3 bg-amber-100 text-amber-700">Layanan Publik</span>
      <h1 class="text-3xl font-bold text-green-800 md:text-4xl">Layanan {{ $villageSettings->village_name }}</h1>
      <p class="mt-2 max-w-2xl text-gray-600">Informasi layanan dan tautan yang disediakan oleh pemerintah desa.</p>
    </header>

    @if ($serviceLinks->isEmpty())
      <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow-sm">Informasi layanan belum tersedia.</p>
    @else
      <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($serviceLinks as $serviceLink)
          @if ($serviceLink->safeUrl())
            <a href="{{ $serviceLink->safeUrl() }}" class="group flex h-full gap-4 rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
              <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-700">
                <i class="{{ $serviceLink->icon ?: 'fas fa-link' }} text-xl" aria-hidden="true"></i>
              </span>
              <span>
                <span class="block text-lg font-bold text-gray-900 transition group-hover:text-green-700">{{ $serviceLink->title }}</span>
                @if ($serviceLink->description)
                  <span class="mt-2 block text-sm leading-6 text-gray-600">{{ $serviceLink->description }}</span>
                @endif
              </span>
            </a>
          @endif
        @endforeach
      </div>
    @endif
  </div>
</main>

@include('partials.home.footer')
@endsection
