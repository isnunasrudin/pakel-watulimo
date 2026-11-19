@if ($pillars !== [])

<div id="pilar-desa" class="rounded-2xl bg-white p-4 shadow">
  <div class="mb-3 flex items-center justify-between gap-3">
    <h3 class="section-title font-bold text-green-800">Pilar Desa</h3>
    <a href="{{ route('pages.officials') }}#pilar-desa" class="text-xs font-semibold text-green-700 hover:underline">Selengkapnya</a>
  </div>

    <div class="swiper pilarSwiper overflow-hidden rounded-xl">
      <div class="swiper-wrapper">
        @foreach ($pillars as $pillar)
          <article class="swiper-slide text-center">
            <button
              type="button"
              data-pillar-image="{{ $pillar['photo_url'] }}"
              data-pillar-name="{{ $pillar['name'] }}"
              data-pillar-position="{{ $pillar['position'] }}"
              aria-label="Lihat foto {{ $pillar['name'] }}"
              aria-haspopup="dialog"
              class="group relative mx-auto block w-full overflow-hidden rounded-xl bg-gray-100"
              style="background-color: {{ $pillar['background_color'] }}"
            >
              <img src="{{ $pillar['photo_url'] }}" alt="Foto {{ $pillar['name'] }}" class="block h-auto w-full transition duration-500 group-hover:scale-105" loading="lazy">
              <span class="absolute inset-0 flex items-center justify-center bg-black/0 text-white opacity-0 transition group-hover:bg-black/25 group-hover:opacity-100">
                <i class="fas fa-expand text-xl" aria-hidden="true"></i>
              </span>
            </button>
            <h4 class="mt-2 text-sm font-bold text-gray-900">{{ $pillar['name'] }}</h4>
            <p class="text-xs text-gray-500">{{ $pillar['position'] }}</p>
          </article>
        @endforeach
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>

    <dialog id="pilarPhotoLightbox" aria-label="Pratinjau foto pilar desa" aria-describedby="pilarLightboxCaption" class="w-11/12 max-w-3xl overflow-hidden rounded-2xl border-0 bg-gray-950 p-0 text-white shadow-2xl">
      <div class="relative">
        <button id="closePilarLightbox" type="button" class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80" aria-label="Tutup pratinjau foto">
          <i class="fas fa-xmark text-lg" aria-hidden="true"></i>
        </button>
        <img id="pilarLightboxImage" src="" alt="" class="max-h-[78vh] w-full bg-black object-contain">
        <p id="pilarLightboxCaption" aria-live="polite" class="px-5 py-4 text-center text-sm text-gray-200"></p>
      </div>
    </dialog>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const slider = document.querySelector('.pilarSwiper');
  let swiper = null;

  if (slider && typeof Swiper !== 'undefined') {
    const slideCount = slider.querySelectorAll('.swiper-slide').length;

    swiper = new Swiper(slider, {
      loop: slideCount > 1,
      autoHeight: true,
      slidesPerView: 2,
      spaceBetween: 12,
      navigation: { nextEl: '.pilarSwiper .swiper-button-next', prevEl: '.pilarSwiper .swiper-button-prev' },
      autoplay: slideCount > 1 ? { delay: 3000, disableOnInteraction: false } : false,
    });
  }

  const lightbox = document.getElementById('pilarPhotoLightbox');
  const lightboxImage = document.getElementById('pilarLightboxImage');
  const lightboxCaption = document.getElementById('pilarLightboxCaption');
  const closeLightbox = document.getElementById('closePilarLightbox');
  const buttons = document.querySelectorAll('[data-pillar-image]');

  if (!lightbox || !lightboxImage || !lightboxCaption || buttons.length === 0) return;

  let lastFocusedPillar = null;

  const setPillarScrollLocked = (locked) => {
    document.body.style.overflow = locked ? 'hidden' : '';
  };

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      lastFocusedPillar = button;
      lightboxImage.src = button.dataset.pillarImage;
      lightboxImage.alt = 'Foto ' + button.dataset.pillarName;
      lightboxCaption.textContent = [button.dataset.pillarName, button.dataset.pillarPosition].filter(Boolean).join(' — ');
      lightbox.showModal();
      setPillarScrollLocked(true);
      closeLightbox?.focus();
      swiper?.autoplay?.stop();
    });
  });

  closeLightbox?.addEventListener('click', () => lightbox?.close());
  lightbox?.addEventListener('click', (event) => {
    if (event.target === lightbox) lightbox.close();
  });
  lightbox?.addEventListener('close', () => {
    setPillarScrollLocked(false);
    lastFocusedPillar?.focus();
    swiper?.autoplay?.start();
  });
});
</script>
@endpush
@endif
