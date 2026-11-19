@if ($albums->isNotEmpty())

<div id="galeri" class="rounded-2xl bg-white p-4 shadow">
  <div class="mb-3 flex items-center justify-between gap-3">
    <h3 class="section-title font-bold text-green-800">Galeri Foto</h3>
    <a href="{{ route('gallery.photos') }}" class="text-xs font-semibold text-green-700 hover:underline">Semua album</a>
  </div>

    <div class="swiper galeriFotoSwiper overflow-hidden rounded-xl">
      <div class="swiper-wrapper">
        @foreach ($albums as $album)
          <a href="{{ route('gallery.album', $album) }}" class="swiper-slide group relative block overflow-hidden rounded-xl" aria-label="Buka album {{ $album->title }}">
            <img src="{{ $album->coverThumbUrl() }}" alt="" class="h-44 w-full object-cover">
            <div class="absolute inset-0 flex items-center justify-center bg-black/40 px-3 text-center transition group-hover:bg-black/55">
              <div>
                <span class="font-bold text-sm text-white">{{ $album->title }}</span>
                <span class="mt-1 block text-xs text-gray-200">{{ $album->photos_count }} foto</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (!document.querySelector('.galeriFotoSwiper') || typeof Swiper === 'undefined') return;
  new Swiper('.galeriFotoSwiper', {
    loop: {{ $albums->count() > 1 ? 'true' : 'false' }},
    slidesPerView: 1,
    spaceBetween: 8,
    navigation: { nextEl: '.galeriFotoSwiper .swiper-button-next', prevEl: '.galeriFotoSwiper .swiper-button-prev' },
    autoplay: {{ $albums->count() > 1 ? '{ delay: 3500, disableOnInteraction: false }' : 'false' }}
  });
});
</script>
@endpush
@endif
