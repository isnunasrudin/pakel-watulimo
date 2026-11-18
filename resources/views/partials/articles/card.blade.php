<article @class([
    'relative flex flex-col overflow-hidden rounded-2xl bg-white shadow card-hover sm:flex-row sm:min-h-[240px]',
    'border border-amber-300/80 shadow-lg ring-1 ring-amber-300/60' => $article->is_pinned,
  ])>
  @if ($article->is_pinned)
    <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-green-700" aria-hidden="true"></span>
  @endif
  <a href="{{ route('articles.show', $article) }}" class="relative w-full sm:w-56 flex-shrink-0 overflow-hidden h-40 sm:h-auto" aria-label="Baca {{ $article->title }}">
    <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
  </a>
  <div class="p-6 flex flex-1 flex-col">
    <div class="flex flex-wrap gap-2">
      @if ($article->is_pinned)
        <span class="badge bg-amber-500 text-white shadow-sm"><i class="fas fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>
      @endif
      @if ($article->category)
        <a href="{{ route('categories.show', $article->category) }}" class="badge {{ $article->category->badgeClasses() }} transition hover:opacity-80">{{ $article->category->name }}</a>
      @endif
    </div>
    <h3 @class([
      'mt-2 mb-2 line-clamp-2 min-h-[3.5rem] font-bold',
      'text-xl leading-snug text-gray-900 sm:text-2xl' => $article->is_pinned,
      'text-lg' => ! $article->is_pinned,
    ])>
      <a href="{{ route('articles.show', $article) }}" class="transition hover:text-green-700">{{ $article->title }}</a>
    </h3>
    <p class="text-sm text-gray-600 line-clamp-3 mb-3 min-h-[3.75rem]">{{ $article->summary(180) }}</p>
    <div class="flex items-center justify-between text-xs text-gray-500 mt-auto">
      <span><i class="fas fa-calendar-alt mr-1 text-sm" aria-hidden="true"></i>{{ ($article->published_at ?? $article->created_at)->format('d M Y') }}</span>
      <span><i class="fas fa-eye mr-1 text-sm" aria-hidden="true"></i>{{ number_format($article->views_count, 0, ',', '.') }} dibaca</span>
    </div>
    <a href="{{ route('articles.show', $article) }}" @class([
      'mt-3 inline-flex self-start items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition',
      'bg-green-800 shadow-sm hover:bg-green-900' => $article->is_pinned,
      'bg-green-700 hover:bg-green-800' => ! $article->is_pinned,
    ])>Baca Selengkapnya <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i></a>
  </div>
</article>
