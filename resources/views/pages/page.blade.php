@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title.' | '.$villageSettings->village_name)
@section('meta_description', $page->meta_description ?: $page->summary())
@section('canonical', route('pages.show', $page))
@section('og_title', $page->meta_title ?: $page->title)
@section('og_image', $page->coverUrl() ? url($page->coverUrl()) : '')

@section('content')
@include('partials.home.navbar')

<main class="bg-white py-10 md:bg-gray-50 md:py-14">
  <div class="mx-auto max-w-7xl px-4 md:px-6">
    <nav aria-label="Breadcrumb" class="mb-6 flex flex-wrap items-center gap-2 text-sm text-gray-500">
      <a href="{{ route('home') }}" class="transition hover:text-green-700">Beranda</a>
      <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
      <span aria-current="page" class="text-gray-700">{{ $page->title }}</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-3">
      <div class="min-w-0 lg:col-span-2">
    <article class="bg-transparent md:overflow-hidden md:rounded-2xl md:bg-white md:shadow-lg">
      @if ($page->coverUrl())
        <figure>
          <button
            type="button"
            data-content-image="{{ $page->coverUrl() }}"
            data-content-image-caption="{{ $page->title }}"
            aria-label="Perbesar gambar sampul: {{ $page->title }}"
            aria-haspopup="dialog"
            class="group relative block w-full overflow-hidden bg-gray-100 text-left"
          >
            <img src="{{ $page->coverUrl() }}" alt="{{ $page->title }}" class="aspect-[16/9] w-full object-cover transition duration-500 group-hover:scale-[1.01]">
            <span class="absolute inset-0 flex items-center justify-center bg-black/0 text-white opacity-0 transition group-hover:bg-black/25 group-hover:opacity-100" aria-hidden="true">
              <i class="fas fa-expand text-xl"></i>
            </span>
          </button>
        </figure>
      @endif

      <header class="py-5 md:p-10">
        <span class="badge mb-3 bg-amber-100 text-amber-700">Halaman</span>
        <h1 class="text-3xl font-bold leading-tight text-gray-900 md:text-4xl">{{ $page->title }}</h1>

        @if ($page->excerpt)
          <p class="mt-4 max-w-3xl text-lg leading-8 text-gray-700">{{ $page->excerpt }}</p>
        @endif

        <p class="mt-4 text-sm text-gray-500">
          <i class="fas fa-clock mr-2 text-green-700" aria-hidden="true"></i>
          Diperbarui <time datetime="{{ $page->updated_at->toDateString() }}">{{ $page->updated_at->locale('id')->translatedFormat('d F Y') }}</time>
        </p>
      </header>

      <div class="px-0 pb-8 md:px-10 md:pb-10">
        @if (filled($page->content))
          <div class="article-body">
            {!! $page->renderRichContent('content') !!}
          </div>
        @else
          <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">Konten halaman belum tersedia.</p>
        @endif

        @include('partials.attachments', ['attachments' => $page->getMedia('attachments')])
      </div>
    </article>
      </div>

      @include('partials.home.sidebar')
    </div>
  </div>
</main>

@include('partials.content-image-lightbox')
@include('partials.responsive-tables')
@include('partials.home.footer')
@endsection
