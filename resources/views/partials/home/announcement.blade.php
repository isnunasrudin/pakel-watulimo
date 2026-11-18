
@if ($announcement)
<section class="py-6">
<div class="max-w-7xl mx-auto px-4 md:px-6">
  <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-2xl shadow-lg p-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl flex-shrink-0"><i class="fas fa-bullhorn text-xl" aria-hidden="true"></i></div>
      <div>
        <h3 class="font-bold text-lg">{{ $announcement->title }}</h3>
        <p class="text-sm text-amber-100">{{ $announcement->body }}</p>
      </div>
    </div>
    @if ($announcement->link_url)
      <a href="{{ $announcement->link_url }}" class="bg-white text-amber-600 font-semibold px-5 py-2 rounded-lg hover:bg-amber-50 flex-shrink-0">{{ $announcement->link_label ?: 'Selengkapnya' }} <i class="fas fa-arrow-right ml-1 text-sm" aria-hidden="true"></i></a>
    @endif
  </div>
</div>
</section>
@endif
