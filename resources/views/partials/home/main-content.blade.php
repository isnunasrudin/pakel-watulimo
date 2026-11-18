
<section id="profil" class="py-12">
<div class="max-w-7xl mx-auto px-4 md:px-6">
  <div class="grid lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 space-y-8">
      
      @php
        $sliderItems = $homeSlides->map(fn ($slide): array => [
            'image' => $slide->imageUrl(),
            'title' => $slide->resolvedTitle(),
            'description' => $slide->resolvedDescription(),
            'alt' => $slide->resolvedTitle(),
            'link' => $slide->resolvedLinkUrl(),
            'external' => $slide->hasExternalLink(),
        ])->all();

        if ($sliderItems === []) {
            $sliderItems = $albums->take(3)->map(fn ($album): array => [
                'image' => $album->coverUrl(),
                'title' => $album->title,
                'description' => $album->description ?: $album->photos_count.' foto',
                'alt' => 'Sampul album '.$album->title,
                'link' => route('gallery.album', $album),
                'external' => false,
            ])->all();
        }
      @endphp

      @if ($sliderItems !== [])
        <div class="swiper selayangSwiper rounded-2xl overflow-hidden shadow-lg">
          <div class="swiper-wrapper">
            @foreach ($sliderItems as $slide)
              <div class="swiper-slide relative">
                @if ($slide['link'])
                  <a href="{{ $slide['link'] }}" @if ($slide['external']) target="_blank" rel="noopener noreferrer" @endif aria-label="Buka {{ $slide['title'] }}" class="group relative block">
                @endif
                    <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}" class="h-72 w-full object-cover sm:h-80 md:h-[26rem]">
                    <div class="absolute inset-0 flex items-end bg-gradient-to-t from-black/70 to-transparent p-5">
                      <div class="min-w-0 text-white">
                        <h3 class="line-clamp-2 text-xl font-bold leading-snug" title="{{ $slide['title'] }}">{{ $slide['title'] }}</h3>
                        @if (filled($slide['description']))
                          <p class="line-clamp-2 text-sm text-gray-200">{{ \Illuminate\Support\Str::limit($slide['description'], 100) }}</p>
                        @endif
                      </div>
                    </div>
                @if ($slide['link'])
                  </a>
                @endif
              </div>
            @endforeach
          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
      @endif

      
      <div id="berita">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-bold section-title">{{ $homeCopy['news_heading'] }}</h2>
          <a href="{{ route('articles.index') }}" class="text-green-700 font-semibold text-sm hover:underline">{{ $homeCopy['news_link_label'] }} <i class="fas fa-arrow-right ml-1 text-sm" aria-hidden="true"></i></a>
        </div>
        <div class="flex flex-col gap-8">
          @forelse ($articles as $article)
            @include('partials.articles.card', ['article' => $article])
          @empty
            <p class="rounded-2xl bg-white p-6 text-sm text-gray-600 shadow">Belum ada artikel yang diterbitkan.</p>
          @endforelse
        </div>

        @if ($articles->hasPages())
          <div class="mt-8">
            {{ $articles->links() }}
          </div>
        @endif
      </div>
    </div>

    
    <aside class="space-y-6">
      @include('partials.widgets.prakiraan-cuaca')
      @include('partials.widgets.statistik-pengunjung')
      @include('partials.widgets.perangkat-desa')
      @include('partials.widgets.pilar-desa')
      @include('partials.widgets.galeri-foto')
      @include('partials.widgets.galeri-video')
      @include('partials.widgets.sinergi-program')
      @include('partials.widgets.agenda-desa')
      @include('partials.widgets.media-sosial')
      @include('partials.widgets.kategori-artikel')
      @include('partials.widgets.arsip-artikel')
    </aside>
  </div>

</div>
</section>

@push('scripts')
<script>
function initSwiper(sel, opts){ if(!document.querySelector(sel)) return; return new Swiper(sel, opts); }
document.addEventListener('DOMContentLoaded', function () {
  const slider = document.querySelector('.selayangSwiper');
  if (!slider) return;

  const slideCount = slider.querySelectorAll('.swiper-slide').length;

  initSwiper('.selayangSwiper', {
    loop: slideCount > 1,
    autoplay: slideCount > 1 ? { delay: 4000, disableOnInteraction: false } : false,
    navigation: { nextEl: '.selayangSwiper .swiper-button-next', prevEl: '.selayangSwiper .swiper-button-prev' },
    effect: 'fade', fadeEffect: { crossFade: true }
  });
});
</script>
@endpush
