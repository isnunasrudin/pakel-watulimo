@extends('layouts.app')

@isset($category)
  @section('title', $category->name.' | Berita Desa Pakel')
  @section('meta_description', $category->description ?: 'Artikel kategori '.$category->name.' dari Desa Pakel.')
  @section('canonical', route('categories.show', $category))
  @section('og_title', $category->name.' | Berita Desa Pakel')
  @section('og_image', count($articles) > 0 ? url($articles->first()->coverUrl()) : '')
@else
  @section('title', 'Berita & Artikel Desa Pakel | Website Resmi Pemerintah Desa')
  @section('meta_description', 'Berita dan artikel terbaru dari Desa Pakel, Watulimo, Trenggalek: pembangunan, pemerintahan, sosial, dan kegiatan warga.')
  @section('canonical', route('articles.index'))
  @section('og_title', 'Berita & Artikel Desa Pakel')
  @section('og_image', count($articles) > 0 ? url($articles->first()->coverUrl()) : '')
@endisset

@section('content')
@include('partials.home.navbar')

<main class="bg-gray-50 py-10 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      @isset($category)
        <a href="{{ route('articles.index') }}" class="transition hover:text-green-700">Berita</a>
        <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
        <span aria-current="page" class="text-gray-700">{{ $category->name }}</span>
      @else
        <span aria-current="page" class="text-gray-700">Berita</span>
      @endisset
    </nav>

    <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <span class="badge mb-3 bg-amber-100 text-amber-700">Kabar Desa</span>
        <h1 class="text-3xl font-bold text-green-800 md:text-4xl">
          @isset($category)
            Berita {{ $category->name }}
          @else
            Berita &amp; Artikel Terbaru
          @endisset
        </h1>
        <p class="mt-2 max-w-2xl text-gray-600">
          @isset($category)
            {{ $category->description ?: 'Kumpulan artikel pada kategori '.$category->name.'.' }}
          @else
            Informasi kegiatan, pembangunan, dan layanan masyarakat Desa Pakel.
          @endisset
        </p>
      </div>
      <span class="rounded-full bg-green-50 px-4 py-2 text-sm font-semibold text-green-700">{{ $articles->total() }} artikel</span>
    </header>

    <form method="GET" action="{{ route('articles.index') }}" class="mb-8 flex max-w-md gap-2">
      <label for="article-search" class="sr-only">Cari artikel</label>
      <input id="article-search" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Cari artikel..."
        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
      <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
        <i class="fas fa-search" aria-hidden="true"></i>Cari
      </button>
    </form>

    @if ($articles->isEmpty())
      <p class="rounded-2xl bg-white p-8 text-center text-sm text-gray-600 shadow">Belum ada artikel yang sesuai.</p>
    @else
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($articles as $article)
          <article @class([
            'flex flex-col overflow-hidden rounded-2xl bg-white shadow card-hover',
            'relative border border-amber-300/80 shadow-lg ring-1 ring-amber-300/60' => $article->is_pinned,
          ])>
            @if ($article->is_pinned)
              <span class="absolute inset-x-0 top-0 z-10 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-green-700" aria-hidden="true"></span>
            @endif
            <a href="{{ route('articles.show', $article) }}" class="relative block aspect-[16/10] overflow-hidden bg-gray-100">
              <img src="{{ $article->coverUrl() }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy">
            </a>
            <div class="flex flex-1 flex-col p-5">
              <div class="flex flex-wrap gap-2">
                @if ($article->is_pinned)
                  <span class="badge bg-amber-500 text-white shadow-sm"><i class="fas fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>
                @endif
                @if ($article->category)
                  <a href="{{ route('categories.show', $article->category) }}" class="badge {{ $article->category->badgeClasses() }}">{{ $article->category->name }}</a>
                @endif
              </div>
              <h2 @class([
                'mt-3 font-bold leading-snug',
                'text-xl text-gray-900' => $article->is_pinned,
                'text-lg' => ! $article->is_pinned,
              ])>
                <a href="{{ route('articles.show', $article) }}" class="transition hover:text-green-700">{{ $article->title }}</a>
              </h2>
              <p class="mt-2 hidden text-sm text-gray-600 md:line-clamp-3">{{ $article->summary(150) }}</p>
              <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4 text-xs text-gray-500">
                <span><i class="fas fa-calendar-alt mr-1" aria-hidden="true"></i>{{ ($article->published_at ?? $article->created_at)->format('d M Y') }}</span>
                <span><i class="fas fa-eye mr-1" aria-hidden="true"></i>{{ number_format($article->views_count, 0, ',', '.') }} dibaca</span>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      <div class="mt-10">
        {{ $articles->onEachSide(1)->links('partials.pagination') }}
      </div>
    @endif
  </div>
</main>

@include('partials.home.footer')
@endsection
