@extends('layouts.app')

@section('title', 'Album Foto: '.$album->title)
@section('meta_description', $album->description ?: 'Album foto '.$album->title.' dari Desa Pakel.')
@section('canonical', route('gallery.album', $album))
@section('og_image', url($album->coverUrl()))

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <a href="{{ route('gallery.photos') }}" class="transition hover:text-green-700">Galeri Foto</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">{{ $album->title }}</span>
    </nav>

    <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <span class="badge mb-3 bg-amber-100 text-amber-700">Album Foto</span>
        <h1 class="text-3xl font-bold text-green-800 md:text-4xl">{{ $album->title }}</h1>
        @if ($album->description)
          <p class="mt-2 max-w-2xl text-gray-600">{{ $album->description }}</p>
        @endif
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <span class="rounded-full bg-green-50 px-4 py-2 text-sm font-semibold text-green-700">{{ $album->photos_count }} foto</span>
        <a href="{{ route('gallery.photos') }}" class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 text-sm font-semibold text-green-700 transition hover:bg-green-50">
          <i class="fas fa-arrow-left" aria-hidden="true"></i>Semua album
        </a>
      </div>
    </header>

    @if ($photos->isEmpty())
      <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow">Belum ada foto pada album ini.</p>
    @else
      <div class="grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
        @foreach ($photos as $photo)
          <figure class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <button
              type="button"
              data-gallery-image="{{ $photo->url() }}"
              data-gallery-caption="{{ $photo->caption ?: $album->title }}"
              aria-label="Lihat foto: {{ $photo->caption ?: $album->title }}"
              class="group relative block aspect-[4/3] w-full overflow-hidden bg-gray-100"
            >
              <img src="{{ $photo->thumbUrl() }}" alt="{{ $photo->caption ?: $album->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
              <span class="absolute inset-0 flex items-center justify-center bg-black/0 text-white opacity-0 transition group-hover:bg-black/25 group-hover:opacity-100">
                <i class="fas fa-expand text-xl" aria-hidden="true"></i>
              </span>
            </button>
            @if ($photo->caption || $photo->taken_at)
              <figcaption class="p-3 sm:p-4">
                @if ($photo->caption)
                  <h2 class="font-semibold text-gray-900">{{ $photo->caption }}</h2>
                @endif
                @if ($photo->taken_at)
                  <p class="mt-1 text-xs text-gray-500">{{ $photo->taken_at->format('d F Y') }}</p>
                @endif
              </figcaption>
            @endif
          </figure>
        @endforeach
      </div>

      <div class="mt-10">
        {{ $photos->onEachSide(1)->links('partials.pagination') }}
      </div>
    @endif
  </div>
</main>

<dialog id="photoLightbox" aria-label="Pratinjau foto" aria-describedby="lightboxCaption" class="w-11/12 max-w-6xl overflow-hidden rounded-2xl border-0 bg-gray-950 p-0 text-white shadow-2xl">
  <div class="relative">
    <button id="closePhotoLightbox" type="button" class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80" aria-label="Tutup pratinjau foto">
      <i class="fas fa-xmark text-lg" aria-hidden="true"></i>
    </button>
    <img id="lightboxImage" src="" alt="" class="max-h-[78vh] w-full bg-black object-contain">
    <p id="lightboxCaption" aria-live="polite" class="px-5 py-4 text-center text-sm text-gray-200"></p>
  </div>
</dialog>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const photoLightbox = document.getElementById('photoLightbox');
  const lightboxImage = document.getElementById('lightboxImage');
  const lightboxCaption = document.getElementById('lightboxCaption');
  const closePhotoLightbox = document.getElementById('closePhotoLightbox');
  let lastFocusedPhoto = null;

  const setPageScrollLocked = (locked) => {
    document.body.style.overflow = locked ? 'hidden' : '';
  };

  document.querySelectorAll('[data-gallery-image]').forEach((photoButton) => {
    photoButton.addEventListener('click', () => {
      lastFocusedPhoto = photoButton;
      lightboxImage.src = photoButton.dataset.galleryImage;
      lightboxImage.alt = photoButton.dataset.galleryCaption;
      lightboxCaption.textContent = photoButton.dataset.galleryCaption;
      photoLightbox.showModal();
      setPageScrollLocked(true);
      closePhotoLightbox.focus();
    });
  });

  closePhotoLightbox.addEventListener('click', () => photoLightbox.close());
  photoLightbox.addEventListener('click', (event) => {
    if (event.target === photoLightbox) {
      photoLightbox.close();
    }
  });
  photoLightbox.addEventListener('close', () => {
    setPageScrollLocked(false);
    lastFocusedPhoto?.focus();
  });
});
</script>
@endpush

@include('partials.home.footer')
@endsection
