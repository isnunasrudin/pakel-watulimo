@extends('layouts.app')

@section('title', 'Pemerintahan Desa')
@section('meta_description', 'Susunan perangkat desa dan pemerintahan '.$villageSettings->village_name.', Kecamatan '.$villageSettings->district_name.'.')
@section('canonical', route('pages.officials'))

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
              <button
                type="button"
                data-pillar-image="{{ $pillar->photoUrl() }}"
                data-pillar-name="{{ $pillar->name }}"
                data-pillar-position="{{ $pillar->position }}"
                aria-label="Perbesar foto {{ $pillar->name }}"
                aria-haspopup="dialog"
                class="group relative block w-full overflow-hidden text-left"
                style="background-color: {{ $pillar->backgroundColor() }}"
              >
                <img src="{{ $pillar->photoUrl() }}" alt="Foto {{ $pillar->name }}" class="block h-auto w-full transition duration-500 group-hover:scale-105" loading="lazy">
                <span class="absolute inset-0 flex items-center justify-center bg-black/0 text-white opacity-0 transition group-hover:bg-black/25 group-hover:opacity-100" aria-hidden="true">
                  <i class="fas fa-expand text-xl"></i>
                </span>
              </button>
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

<dialog id="pilarPhotoLightbox" aria-label="Pratinjau foto pilar desa" aria-describedby="pilarLightboxCaption" class="w-11/12 max-w-3xl overflow-hidden rounded-2xl border-0 bg-gray-950 p-0 text-white shadow-2xl">
  <div class="relative">
    <button id="closePilarLightbox" type="button" class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80" aria-label="Tutup pratinjau foto">
      <i class="fas fa-xmark text-lg" aria-hidden="true"></i>
    </button>
    <img id="pilarLightboxImage" src="" alt="" class="max-h-[78vh] w-full bg-black object-contain">
    <p id="pilarLightboxCaption" aria-live="polite" class="px-5 py-4 text-center text-sm text-gray-200"></p>
  </div>
</dialog>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const lightbox = document.getElementById('pilarPhotoLightbox');
  const lightboxImage = document.getElementById('pilarLightboxImage');
  const lightboxCaption = document.getElementById('pilarLightboxCaption');
  const closeLightbox = document.getElementById('closePilarLightbox');
  const buttons = document.querySelectorAll('[data-pillar-image]');

  if (!lightbox || !lightboxImage || !lightboxCaption || buttons.length === 0) return;

  let lastFocusedPillar = null;

  const setScrollLocked = (locked) => {
    document.body.style.overflow = locked ? 'hidden' : '';
  };

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      lastFocusedPillar = button;
      lightboxImage.src = button.dataset.pillarImage;
      lightboxImage.alt = 'Foto ' + button.dataset.pillarName;
      lightboxCaption.textContent = [button.dataset.pillarName, button.dataset.pillarPosition].filter(Boolean).join(' — ');
      lightbox.showModal();
      setScrollLocked(true);
      closeLightbox?.focus();
    });
  });

  closeLightbox?.addEventListener('click', () => lightbox.close());
  lightbox.addEventListener('click', (event) => {
    if (event.target === lightbox) lightbox.close();
  });
  lightbox.addEventListener('close', () => {
    setScrollLocked(false);
    lastFocusedPillar?.focus();
  });
});
</script>
@endpush

@include('partials.home.footer')
@endsection
