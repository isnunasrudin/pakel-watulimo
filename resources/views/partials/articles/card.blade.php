<article class="bg-white rounded-2xl shadow overflow-hidden card-hover flex flex-col sm:flex-row sm:min-h-[240px]">
  <a href="{{ route('articles.show', $article) }}" class="relative w-full sm:w-56 flex-shrink-0 overflow-hidden h-40 sm:h-auto" aria-label="Baca {{ $article->title }}">
    <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
  </a>
  <div class="p-6 flex flex-1 flex-col">
    <div class="flex flex-wrap gap-2">
      @if ($article->is_pinned)
        <span class="badge bg-amber-100 text-amber-700"><i class="fas fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>
      @endif
      @if ($article->category)
        <a href="{{ route('categories.show', $article->category) }}" class="badge {{ $article->category->badgeClasses() }} transition hover:opacity-80">{{ $article->category->name }}</a>
      @endif
    </div>
    <h3 class="font-bold text-lg mt-2 mb-2 line-clamp-2 min-h-[3.5rem]">
      <a href="{{ route('articles.show', $article) }}" class="transition hover:text-green-700">{{ $article->title }}</a>
    </h3>
    <p class="text-sm text-gray-600 line-clamp-3 mb-3 min-h-[3.75rem]">{{ $article->summary(180) }}</p>
    <div class="flex items-center justify-between text-xs text-gray-500 mt-auto">
      <span><i class="fas fa-calendar-alt mr-1 text-sm" aria-hidden="true"></i>{{ ($article->published_at ?? $article->created_at)->format('d M Y') }}</span>
      <span><i class="fas fa-eye mr-1 text-sm" aria-hidden="true"></i>{{ number_format($article->views_count, 0, ',', '.') }} dibaca</span>
    </div>
    <a href="{{ route('articles.show', $article) }}" class="mt-3 inline-flex self-start items-center gap-2 rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-800">Baca Selengkapnya <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i></a>
  </div>
</article>
