@if ($videos->isNotEmpty())

<div id="galeri-video" class="rounded-2xl bg-white p-4 shadow">
  <div class="mb-3 flex items-center justify-between gap-3">
    <h3 class="section-title font-bold text-green-800">Galeri Video</h3>
    <a href="{{ route('gallery.videos') }}" class="text-xs font-semibold text-green-700 hover:underline">Semua video</a>
  </div>

    <div class="swiper galeriVideoSwiper overflow-hidden rounded-xl">
      <div class="swiper-wrapper">
        @foreach ($videos as $video)
          <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="swiper-slide group block overflow-hidden rounded-xl" aria-label="Tonton {{ $video->title }} di tab baru">
            <div class="relative aspect-video overflow-hidden">
              <img src="{{ $video->thumbnailUrl() }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
              <div class="absolute inset-0 flex items-center justify-center bg-black/30 transition group-hover:bg-black/45">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 text-green-700">
                  <i class="fas fa-play text-xl" aria-hidden="true"></i>
                </span>
              </div>
            </div>
            <div class="p-2">
              <h4 class="line-clamp-2 text-xs font-semibold text-gray-900">{{ $video->title }}</h4>
              <p class="mt-1 text-xs text-gray-500">{{ $video->published_at->format('d M Y') }}</p>
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
  if (!document.querySelector('.galeriVideoSwiper') || typeof Swiper === 'undefined') return;
  new Swiper('.galeriVideoSwiper', {
    loop: {{ $videos->count() > 1 ? 'true' : 'false' }},
    slidesPerView: 1,
    spaceBetween: 8,
    navigation: { nextEl: '.galeriVideoSwiper .swiper-button-next', prevEl: '.galeriVideoSwiper .swiper-button-prev' }
  });
});
</script>
@endpush
@endif
