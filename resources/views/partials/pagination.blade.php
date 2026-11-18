@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-center gap-2">
        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-300" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:border-green-300 hover:bg-green-50 hover:text-green-700" aria-label="Halaman sebelumnya">
                    <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        <p class="text-xs font-medium text-gray-500 sm:hidden">
            Halaman <span class="font-semibold text-gray-700">{{ $paginator->currentPage() }}</span> / {{ $paginator->lastPage() }}
        </p>

        <div class="hidden items-center gap-1.5 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-9 min-w-9 items-center justify-center px-1 text-xs text-gray-400" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-green-700 bg-green-700 px-2 text-sm font-semibold text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-gray-200 bg-white px-2 text-sm font-medium text-gray-600 transition hover:border-green-300 hover:bg-green-50 hover:text-green-700" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="flex items-center gap-1.5">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:border-green-300 hover:bg-green-50 hover:text-green-700" aria-label="Halaman berikutnya">
                    <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                </a>
            @else
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-300" aria-disabled="true" aria-label="Halaman berikutnya">
                    <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
