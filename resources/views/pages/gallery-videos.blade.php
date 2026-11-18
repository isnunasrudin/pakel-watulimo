@extends('layouts.app')

@section('title', 'Galeri Video Desa Pakel | Website Resmi Pemerintah Desa')
@section('meta_description', 'Galeri video kegiatan dan dokumentasi Desa Pakel, Watulimo.')
@section('canonical', route('gallery.videos'))
@section('og_title', 'Galeri Video Desa Pakel')
@section('og_image', count($videos) > 0 ? url($videos->first()->thumbnailUrl()) : asset('favicon.ico'))

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Galeri Video</span>
    </nav>

    <header class="mb-8">
      <span class="badge mb-3 bg-amber-100 text-amber-700">Dokumentasi Desa</span>
      <h1 class="text-3xl font-bold text-green-800 md:text-4xl">Galeri Video Desa Pakel</h1>
      <p class="mt-2 max-w-2xl text-gray-600">Dokumentasi kegiatan dan informasi Desa Pakel dalam bentuk video.</p>
    </header>

    @if ($videos->isEmpty())
      <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow">Belum ada video yang diterbitkan.</p>
    @else
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($videos as $video)
          <article class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="group relative block aspect-video overflow-hidden bg-gray-100" aria-label="Tonton {{ $video->title }} di tab baru">
              <img src="{{ $video->thumbnailUrl() }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
              <span class="absolute inset-0 flex items-center justify-center bg-black/30 transition group-hover:bg-black/45">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-green-700">
                  <i class="fas fa-play text-xl" aria-hidden="true"></i>
                </span>
              </span>
            </a>
            <div class="flex flex-1 flex-col p-5">
              <h2 class="text-lg font-bold text-gray-900">{{ $video->title }}</h2>
              @if ($video->description)
                <p class="mt-2 line-clamp-3 text-sm text-gray-600">{{ $video->description }}</p>
              @endif
              <div class="mt-auto flex items-center justify-between gap-3 border-t border-gray-100 pt-4 text-xs text-gray-500">
                <time datetime="{{ $video->published_at->toDateString() }}">{{ $video->published_at->format('d M Y') }}</time>
                <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-green-700 hover:underline">Tonton video</a>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      <div class="mt-10">
        {{ $videos->links() }}
      </div>
    @endif
  </div>
</main>

@include('partials.home.footer')
@endsection
