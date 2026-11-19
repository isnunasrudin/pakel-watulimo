<article @class([
    'relative flex overflow-hidden rounded-2xl bg-white shadow card-hover sm:min-h-[240px]',
    'border border-amber-300/80 shadow-lg ring-1 ring-amber-300/60' => $article->is_pinned,
  ])>
  @if ($article->is_pinned)
    <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-green-700" aria-hidden="true"></span>
  @endif
  <a href="{{ route('articles.show', $article) }}" class="relative w-28 shrink-0 self-stretch overflow-hidden bg-gray-100 sm:w-56" aria-label="Baca {{ $article->title }}">
    <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
  </a>
  <div class="flex min-w-0 flex-1 flex-col p-3 sm:p-6">
    <div class="flex flex-wrap gap-1.5 sm:gap-2">
      @if ($article->is_pinned)
        <span class="badge bg-amber-500 text-white shadow-sm"><i class="fas fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>
      @endif
      @if ($article->category)
        <a href="{{ route('categories.show', $article->category) }}" class="badge {{ $article->category->badgeClasses() }} transition hover:opacity-80">{{ $article->category->name }}</a>
      @endif
    </div>
    <h3 @class([
      'mt-2 mb-1 line-clamp-2 font-bold leading-snug sm:mb-2',
      'text-base text-gray-900 sm:text-2xl' => $article->is_pinned,
      'text-base sm:text-lg' => ! $article->is_pinned,
    ])>
      <a href="{{ route('articles.show', $article) }}" class="transition hover:text-green-700">{{ $article->title }}</a>
    </h3>
    <p class="mt-2 hidden text-sm text-gray-600 mb-3 min-h-[3.75rem] md:line-clamp-3">{{ $article->summary(180) }}</p>
    <div class="mt-2 flex items-center justify-between text-xs text-gray-500 sm:mt-auto">
      <span><i class="fas fa-calendar-alt mr-1 text-sm" aria-hidden="true"></i>{{ ($article->published_at ?? $article->created_at)->format('d M Y') }}</span>
      <span><i class="fas fa-eye mr-1 text-sm" aria-hidden="true"></i>{{ number_format($article->views_count, 0, ',', '.') }} dibaca</span>
    </div>
    <a href="{{ route('articles.show', $article) }}" @class([
      'mt-2 inline-flex self-start items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition sm:mt-3 sm:gap-2 sm:px-4 sm:py-2 sm:text-sm',
      'bg-green-800 shadow-sm hover:bg-green-900' => $article->is_pinned,
      'bg-green-700 hover:bg-green-800' => ! $article->is_pinned,
    ])>Baca <span class="hidden sm:inline">Selengkapnya</span> <i class="fas fa-arrow-right text-xs sm:text-sm" aria-hidden="true"></i></a>
  </div>
</article>
