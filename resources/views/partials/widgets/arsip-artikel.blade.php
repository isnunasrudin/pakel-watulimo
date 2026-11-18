
<div class="bg-white rounded-2xl shadow p-5">
  <h3 class="font-bold text-green-800 mb-3 section-title">Arsip Artikel</h3>

  @if ($articleArchives->isEmpty())
    <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">Belum ada arsip artikel.</p>
  @else
    <ul class="space-y-2 text-sm">
      @foreach ($articleArchives as $archive)
        <li class="flex justify-between" title="{{ $archive['count'] }} artikel">
          <span>{{ $archive['label'] }}</span>
          <span class="text-gray-400">{{ $archive['count'] }}</span>
        </li>
      @endforeach
    </ul>
  @endif
</div>
