@extends('layouts.app')

@section('title', 'Galeri Foto Desa Pakel | Website Resmi Pemerintah Desa')
@section('meta_description', 'Galeri foto kegiatan, pembangunan, dan kehidupan masyarakat Desa Pakel, Watulimo.')
@section('canonical', route('gallery.photos'))
@section('og_title', 'Galeri Foto Desa Pakel')
@section('og_image', count($albums) > 0 ? url($albums->first()->coverUrl()) : asset('favicon.ico'))

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Galeri Foto</span>
    </nav>

    <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <span class="badge mb-3 bg-amber-100 text-amber-700">Dokumentasi Desa</span>
        <h1 class="text-3xl font-bold text-green-800 md:text-4xl">Album Foto Desa Pakel</h1>
        <p class="mt-2 max-w-2xl text-gray-600">Jelajahi dokumentasi kegiatan, pembangunan, layanan, dan budaya masyarakat desa dalam album.</p>
      </div>
      <span class="rounded-full bg-green-50 px-4 py-2 text-sm font-semibold text-green-700">{{ $albums->total() }} album</span>
    </header>

    @if ($albums->isEmpty())
      <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow">Belum ada album foto yang diterbitkan.</p>
    @else
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($albums as $album)
          <a href="{{ route('gallery.album', $album) }}" class="group overflow-hidden rounded-2xl bg-white text-left shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
              <img src="{{ $album->coverThumbUrl() }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
              <span class="absolute bottom-3 right-3 rounded-full bg-black/65 px-3 py-1 text-xs font-semibold text-white">
                <i class="fas fa-images mr-1" aria-hidden="true"></i>{{ $album->photos_count }} foto
              </span>
            </div>
            <div class="p-4">
              <h2 class="text-lg font-bold text-gray-900 transition group-hover:text-green-700">{{ $album->title }}</h2>
              <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $album->description }}</p>
              <div class="mt-4 flex items-center justify-between gap-3 border-t border-gray-100 pt-3 text-xs text-gray-500">
                <span>Diperbarui {{ $album->updated_at->format('d M Y') }}</span>
                <span class="font-semibold text-green-700">Buka album <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></span>
              </div>
            </div>
          </a>
        @endforeach
      </div>

      <div class="mt-10">
        {{ $albums->onEachSide(1)->links('partials.pagination') }}
      </div>
    @endif
  </div>
</main>

@include('partials.home.footer')
@endsection
