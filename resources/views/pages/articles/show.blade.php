@extends('layouts.app')

@section('title', $article->meta_title ?: $article->title.' | Desa Pakel')
@section('meta_description', $article->meta_description ?: $article->summary())
@section('canonical', route('articles.show', $article))
@section('og_type', 'article')
@section('og_title', $article->meta_title ?: $article->title)
@section('og_image', url($article->coverUrl()))

@php
    $villageSettings = app(\App\Services\VillageProfileService::class)->settings();
    $publishedAt = $article->published_at ?? $article->created_at;
    $articleBreadcrumbs = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Berita', 'item' => route('articles.index')],
    ];

    if ($article->category) {
        $articleBreadcrumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $article->category->name, 'item' => route('categories.show', $article->category)];
    }

    $articleBreadcrumbs[] = ['@type' => 'ListItem', 'position' => count($articleBreadcrumbs) + 1, 'name' => $article->title, 'item' => route('articles.show', $article)];

    $articleStructuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            array_filter([
                '@type' => 'Article',
                'headline' => $article->title,
                'description' => $article->meta_description ?: $article->summary(160),
                'image' => [url($article->coverUrl())],
                'datePublished' => $publishedAt?->toIso8601String(),
                'dateModified' => $article->updated_at?->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $article->author?->name ?: $villageSettings->village_name,
                ],
                'publisher' => [
                    '@type' => 'GovernmentOrganization',
                    'name' => $villageSettings->village_name,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $villageSettings->iconUrl() ?: $villageSettings->faviconUrl(),
                    ],
                ],
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('articles.show', $article)],
                'articleSection' => $article->category?->name,
                'inLanguage' => 'id-ID',
                'url' => route('articles.show', $article),
            ], static fn (mixed $value): bool => filled($value)),
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => $articleBreadcrumbs,
            ],
        ],
    ];
@endphp

@push('head')
<meta property="article:published_time" content="{{ $publishedAt?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $article->updated_at?->toIso8601String() }}">
@if ($article->author)
    <meta property="article:author" content="{{ $article->author->name }}">
@endif
@if ($article->category)
    <meta property="article:section" content="{{ $article->category->name }}">
@endif
<script type="application/ld+json">{!! json_encode($articleStructuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
@include('partials.home.navbar')

<main class="bg-white py-10 md:bg-gray-50 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-6 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <a href="{{ route('articles.index') }}" class="transition hover:text-green-700">Berita</a>
      @if ($article->category)
        <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
        <a href="{{ route('categories.show', $article->category) }}" class="transition hover:text-green-700">{{ $article->category->name }}</a>
      @endif
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">Artikel</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-3">
      <div class="min-w-0 lg:col-span-2">
    <article class="bg-transparent md:overflow-hidden md:rounded-2xl md:bg-white md:shadow-lg">
      <header class="py-5 md:p-10">
        <div class="mb-5 flex flex-wrap gap-2">
          @if ($article->is_pinned)
            <span class="badge bg-amber-100 text-amber-700"><i class="fas fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>
          @endif
          @if ($article->category)
            <a href="{{ route('categories.show', $article->category) }}" class="badge {{ $article->category->badgeClasses() }}">{{ $article->category->name }}</a>
          @endif
        </div>

        <h1 class="mb-5 text-3xl font-bold leading-tight text-gray-900 md:text-4xl">
          {{ $article->title }}
        </h1>

        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-500">
            <time datetime="{{ ($article->published_at ?? $article->created_at)->toDateString() }}">
              <i class="fas fa-calendar-alt mr-2 text-green-700" aria-hidden="true"></i>{{ ($article->published_at ?? $article->created_at)->format('d F Y') }}
            </time>
            <span><i class="fas fa-eye mr-2 text-green-700" aria-hidden="true"></i>{{ number_format($article->views_count, 0, ',', '.') }} dibaca</span>
            @if ($article->author)
              <span><i class="fas fa-user mr-2 text-green-700" aria-hidden="true"></i>{{ $article->author->name }}</span>
            @endif
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <span id="shareFeedback" aria-live="polite" class="text-xs text-gray-500"></span>
            <div id="shareContainer" class="relative">
              <button id="shareArticle" type="button" class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 text-sm font-semibold text-green-700 transition hover:bg-green-50" aria-label="Bagikan artikel ini" aria-expanded="false" aria-controls="shareMenu">
                <i class="fas fa-share-nodes" aria-hidden="true"></i>Bagikan
              </button>
              <div id="shareMenu" class="absolute right-0 top-full z-50 mt-2 hidden w-56 rounded-xl border border-gray-100 bg-white p-2 shadow-xl" aria-labelledby="shareArticle">
                <a href="#" data-share-service="whatsapp" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition hover:bg-green-50 hover:text-green-800">
                  <i class="fab fa-whatsapp w-5 text-lg text-green-600" aria-hidden="true"></i>WhatsApp
                </a>
                <button type="button" data-share-action="instagram" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-gray-700 transition hover:bg-pink-50 hover:text-pink-700">
                  <i class="fab fa-instagram w-5 text-lg text-pink-600" aria-hidden="true"></i>Instagram
                </button>
                <a href="#" data-share-service="facebook" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition hover:bg-blue-50 hover:text-blue-700">
                  <i class="fab fa-facebook w-5 text-lg text-blue-600" aria-hidden="true"></i>Facebook
                </a>
                <a href="#" data-share-service="x" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                  <i class="fab fa-x-twitter w-5 text-lg text-gray-900" aria-hidden="true"></i>X
                </a>
                <a href="#" data-share-service="telegram" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition hover:bg-sky-50 hover:text-sky-700">
                  <i class="fab fa-telegram w-5 text-lg text-sky-600" aria-hidden="true"></i>Telegram
                </a>
                <a href="#" data-share-service="email" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition hover:bg-amber-50 hover:text-amber-700">
                  <i class="fas fa-envelope w-5 text-gray-500" aria-hidden="true"></i>Email
                </a>
                <button type="button" data-share-action="copy" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                  <i class="fas fa-link w-5 text-gray-500" aria-hidden="true"></i>Salin Tautan
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <figure>
        <button
          type="button"
          data-content-image="{{ $article->coverUrl() }}"
          data-content-image-caption="{{ $article->title }}"
          aria-label="Perbesar gambar sampul: {{ $article->title }}"
          aria-haspopup="dialog"
          class="group relative block w-full overflow-hidden bg-gray-100 text-left"
        >
          <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}" class="aspect-[16/9] w-full object-cover transition duration-500 group-hover:scale-[1.01]">
          <span class="absolute inset-0 flex items-center justify-center bg-black/0 text-white opacity-0 transition group-hover:bg-black/25 group-hover:opacity-100" aria-hidden="true">
            <i class="fas fa-expand text-xl"></i>
          </span>
        </button>
        @if ($article->category)
          <figcaption class="pt-3 text-sm text-gray-500 md:px-10">
            Artikel kategori {{ $article->category->name }} — Desa Pakel.
          </figcaption>
        @endif
      </figure>

      <div class="px-0 pb-8 pt-6 md:px-10 md:pb-10">
        @if ($article->excerpt)
          <p class="mb-8 text-lg leading-8 text-gray-700">{{ $article->excerpt }}</p>
        @endif

        <div class="article-body">
          {!! $article->renderRichContent('body') !!}
        </div>

        @include('partials.attachments', ['attachments' => $article->getMedia('attachments')])
      </div>

      <section class="border-t border-gray-100 px-0 py-8 md:px-10" aria-labelledby="comments-title">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <h2 id="comments-title" class="text-xl font-bold text-gray-900">Komentar <span class="text-sm font-normal text-gray-500">({{ $article->approvedComments->count() }})</span></h2>
          <button id="commentFormToggle" type="button" class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 text-sm font-semibold text-green-700 transition hover:bg-green-50" aria-expanded="false" aria-controls="commentForm">
            <i class="fas fa-pen" aria-hidden="true"></i><span id="commentFormToggleText">Tulis komentar</span>
          </button>
        </div>

        @if (session('comment_status'))
          <p class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800" role="status">{{ session('comment_status') }}</p>
        @endif

        <div class="mt-6 divide-y divide-gray-100">
          @forelse ($article->approvedComments as $comment)
            @php
              $initials = \Illuminate\Support\Str::of($comment->name)
                  ->explode(' ')
                  ->filter()
                  ->take(2)
                  ->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                  ->implode('');
            @endphp
            <article class="flex gap-4 py-5 first:pt-0 last:pb-0">
              <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-800" aria-hidden="true">{{ $initials }}</div>
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                  <h3 class="font-semibold text-gray-900">{{ $comment->name }}</h3>
                  <time datetime="{{ $comment->created_at->toDateString() }}" class="text-xs text-gray-500">{{ $comment->created_at->format('d F Y') }}</time>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $comment->body }}</p>
              </div>
            </article>
          @empty
            <p class="py-4 text-sm text-gray-500">Belum ada komentar yang disetujui.</p>
          @endforelse
        </div>

        <form id="commentForm" method="POST" action="{{ route('articles.comments.store', $article) }}" class="{{ $errors->any() ? '' : 'hidden' }} mt-7 border-t border-gray-100 pt-6">
          @csrf
          <h3 class="mb-4 text-lg font-bold text-gray-900">Kirim komentar</h3>
          <div class="grid gap-4">
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="commentName" class="mb-1.5 block text-sm font-medium text-gray-700">Nama</label>
                <input id="commentName" name="name" type="text" maxlength="80" required value="{{ old('name') }}" placeholder="Masukkan nama Anda"
                  class="w-full rounded-lg border @error('name') border-red-500 @else border-gray-300 @enderror px-4 py-3 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                @error('name')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label for="commentEmail" class="mb-1.5 block text-sm font-medium text-gray-700">Email <span class="text-gray-400">(opsional)</span></label>
                <input id="commentEmail" name="email" type="email" maxlength="255" value="{{ old('email') }}" placeholder="nama@contoh.com"
                  class="w-full rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror px-4 py-3 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                @error('email')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>
            <div>
              <label for="commentBody" class="mb-1.5 block text-sm font-medium text-gray-700">Komentar</label>
              <textarea id="commentBody" name="body" rows="4" maxlength="1000" required placeholder="Tulis tanggapan Anda tentang artikel ini..."
                class="w-full resize-y rounded-lg border @error('body') border-red-500 @else border-gray-300 @enderror px-4 py-3 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('body') }}</textarea>
              @error('body')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>
            <div class="hidden" aria-hidden="true">
              <label for="commentWebsite">Website</label>
              <input id="commentWebsite" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <p class="text-xs text-gray-500">Komentar tampil setelah disetujui admin desa. Maksimal 5 kiriman per menit.</p>
              <button type="submit" class="inline-flex items-center justify-center gap-2 self-start rounded-lg bg-green-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-800 sm:self-auto">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>Kirim komentar
              </button>
            </div>
          </div>
        </form>
      </section>
    </article>

    @if ($relatedArticles->isNotEmpty())
      <section class="mt-10" aria-labelledby="related-title">
        <h2 id="related-title" class="section-title mb-5 text-2xl font-bold">Artikel Terkait</h2>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($relatedArticles as $related)
            <a href="{{ route('articles.show', $related) }}" class="group overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
              <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                <img src="{{ $related->coverUrl() }}" alt="{{ $related->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
              </div>
              <div class="p-4">
                <h3 class="font-bold leading-snug text-gray-900 transition group-hover:text-green-700">{{ $related->title }}</h3>
                <p class="mt-2 text-xs text-gray-500">{{ ($related->published_at ?? $related->created_at)->format('d M Y') }}</p>
              </div>
            </a>
          @endforeach
        </div>
      </section>
    @endif
      </div>

      @include('partials.home.sidebar-compact')
    </div>
  </div>
</main>

@include('partials.content-image-lightbox')

@push('scripts')
<script>
const shareButton = document.getElementById('shareArticle');
const shareFeedback = document.getElementById('shareFeedback');
const shareContainer = document.getElementById('shareContainer');
const shareMenu = document.getElementById('shareMenu');
const articleUrl = window.location.href;
const articleTitle = document.title;
const encodedArticleUrl = encodeURIComponent(articleUrl);
const encodedArticleTitle = encodeURIComponent(articleTitle);

document.querySelector('[data-share-service="whatsapp"]').href = `https://wa.me/?text=${encodeURIComponent(`${articleTitle} ${articleUrl}`)}`;
document.querySelector('[data-share-service="facebook"]').href = `https://www.facebook.com/sharer/sharer.php?u=${encodedArticleUrl}`;
document.querySelector('[data-share-service="x"]').href = `https://twitter.com/intent/tweet?text=${encodedArticleTitle}&url=${encodedArticleUrl}`;
document.querySelector('[data-share-service="telegram"]').href = `https://t.me/share/url?url=${encodedArticleUrl}&text=${encodedArticleTitle}`;
document.querySelector('[data-share-service="email"]').href = `mailto:?subject=${encodedArticleTitle}&body=${encodeURIComponent(`${articleTitle}\n${articleUrl}`)}`;

function closeShareMenu() {
  shareMenu.classList.add('hidden');
  shareButton.setAttribute('aria-expanded', 'false');
}

async function copyArticleLink(message = 'Tautan artikel berhasil disalin.') {
  try {
    await navigator.clipboard.writeText(articleUrl);
    shareFeedback.textContent = message;
  } catch (error) {
    window.prompt('Salin tautan artikel ini:', articleUrl);
    shareFeedback.textContent = 'Silakan salin tautan artikel dari kotak yang muncul.';
  }
}

shareButton.addEventListener('click', () => {
  const isExpanded = shareButton.getAttribute('aria-expanded') === 'true';
  shareMenu.classList.toggle('hidden', isExpanded);
  shareButton.setAttribute('aria-expanded', String(!isExpanded));
});

shareMenu.querySelectorAll('[data-share-service]').forEach((link) => {
  link.addEventListener('click', closeShareMenu);
});

shareMenu.querySelector('[data-share-action="copy"]').addEventListener('click', async () => {
  closeShareMenu();
  await copyArticleLink();
});

shareMenu.querySelector('[data-share-action="instagram"]').addEventListener('click', async () => {
  window.open('https://www.instagram.com/', '_blank', 'noopener,noreferrer');
  closeShareMenu();
  await copyArticleLink('Tautan disalin. Tempelkan di Instagram untuk membagikan artikel.');
});

document.addEventListener('click', (event) => {
  if (!shareContainer.contains(event.target)) {
    closeShareMenu();
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && shareButton.getAttribute('aria-expanded') === 'true') {
    closeShareMenu();
    shareButton.focus();
  }
});

const commentForm = document.getElementById('commentForm');
const commentFormToggle = document.getElementById('commentFormToggle');

commentFormToggle.addEventListener('click', () => {
  const isExpanded = commentFormToggle.getAttribute('aria-expanded') === 'true';
  commentForm.classList.toggle('hidden', isExpanded);
  commentFormToggle.setAttribute('aria-expanded', String(!isExpanded));
  document.getElementById('commentFormToggleText').textContent = isExpanded ? 'Tulis komentar' : 'Tutup form';

  if (!isExpanded) {
    document.getElementById('commentName').focus();
  }
});

</script>
@endpush

@include('partials.responsive-tables')
@include('partials.home.footer')
@endsection
