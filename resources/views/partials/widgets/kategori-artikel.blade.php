
<div class="bg-white rounded-2xl shadow p-5">
  <h3 class="font-bold text-green-800 mb-3 section-title">Kategori Artikel</h3>

  @if ($categories->isEmpty())
    <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">Belum ada kategori dengan artikel terbit.</p>
  @else
    <ul class="space-y-2 text-sm">
      @foreach ($categories as $category)
        <li>
          <a href="{{ route('categories.show', $category) }}" class="flex items-center justify-between gap-2 hover:text-green-700">
            <span><i class="fas fa-folder mr-2 text-base" aria-hidden="true"></i>{{ $category->name }}</span>
            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs">{{ $category->articles_count }}</span>
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>
