<dialog id="contentImageLightbox" aria-label="Pratinjau gambar" aria-describedby="contentImageCaption" class="w-11/12 max-w-6xl overflow-hidden rounded-2xl border-0 bg-gray-950 p-0 text-white shadow-2xl">
  <div class="relative">
    <button id="closeContentImageLightbox" type="button" class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80" aria-label="Tutup pratinjau gambar">
      <i class="fas fa-xmark text-lg" aria-hidden="true"></i>
    </button>
    <img id="contentImageLightboxImage" src="" alt="" class="max-h-[78vh] w-full bg-black object-contain">
    <p id="contentImageCaption" aria-live="polite" class="px-5 py-4 text-center text-sm text-gray-200"></p>
  </div>
</dialog>

@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const lightbox = document.getElementById('contentImageLightbox');
  const lightboxImage = document.getElementById('contentImageLightboxImage');
  const lightboxCaption = document.getElementById('contentImageCaption');
  const closeLightbox = document.getElementById('closeContentImageLightbox');
  const coverButtons = document.querySelectorAll('[data-content-image]');
  const bodyImages = document.querySelectorAll('.article-body img');

  if (!lightbox || !lightboxImage || !lightboxCaption || (coverButtons.length === 0 && bodyImages.length === 0)) return;

  let lastFocusedImage = null;

  const setScrollLocked = (locked) => {
    document.body.style.overflow = locked ? 'hidden' : '';
  };

  const openLightbox = (src, caption) => {
    if (!src) return;

    lightboxImage.src = src;
    lightboxImage.alt = caption || 'Gambar';
    lightboxCaption.textContent = caption || '';
    lightbox.showModal();
    setScrollLocked(true);
    closeLightbox?.focus();
  };

  coverButtons.forEach((button) => {
    button.addEventListener('click', () => {
      lastFocusedImage = button;
      openLightbox(button.dataset.contentImage, button.dataset.contentImageCaption);
    });
  });

  bodyImages.forEach((image) => {
    image.style.cursor = 'zoom-in';
    image.setAttribute('role', 'button');
    image.setAttribute('tabindex', '0');
    image.setAttribute('aria-haspopup', 'dialog');

    const openImage = () => {
      const figureCaption = image.closest('figure')?.querySelector('figcaption')?.textContent.trim();
      lastFocusedImage = image;
      openLightbox(image.currentSrc || image.src, figureCaption || image.alt || '');
    };

    image.addEventListener('click', (event) => {
      event.preventDefault();
      openImage();
    });

    image.addEventListener('keydown', (event) => {
      if (event.key !== 'Enter' && event.key !== ' ') return;

      event.preventDefault();
      openImage();
    });
  });

  closeLightbox?.addEventListener('click', () => lightbox.close());
  lightbox.addEventListener('click', (event) => {
    if (event.target === lightbox) lightbox.close();
  });
  lightbox.addEventListener('close', () => {
    setScrollLocked(false);
    lastFocusedImage?.focus();
  });
});
</script>
@endPushOnce
