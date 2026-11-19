@if ($socialLinks !== [])

<div class="rounded-2xl bg-white p-5 shadow">
  <h3 class="section-title mb-3 font-bold text-green-800">Media Sosial</h3>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
      @foreach ($socialLinks as $socialLink)
        @if ($socialLink['safe_url'])
          <a href="{{ $socialLink['safe_url'] }}" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center rounded-xl bg-gray-50 p-3 text-gray-700 transition hover:bg-green-50 hover:text-green-700" title="{{ $socialLink['platform'] }}">
            <i class="{{ $socialLink['icon'] ?: 'fas fa-globe' }} mb-1 text-2xl" aria-hidden="true"></i>
            <span class="text-xs">{{ $socialLink['platform'] }}</span>
          </a>
        @endif
      @endforeach
    </div>
</div>
@endif
