@php
  $logoUrl = $villageSettings->iconUrl();
@endphp


<nav class="bg-white shadow-md sticky top-0 z-50">
<div class="max-w-7xl mx-auto px-4 md:px-6">
  <div class="flex h-16 items-center justify-between lg:h-20">
    <a href="{{ route('home') }}" class="flex items-center gap-3">
      @if ($logoUrl)
        <img src="{{ $logoUrl }}" alt="Logo {{ $villageSettings->village_name }}" class="h-11 w-11 object-contain">
      @else
        <div class="w-11 h-11 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-lg">{{ \Illuminate\Support\Str::substr($villageSettings->village_name ?: 'Desa Pakel', 0, 1) }}</div>
      @endif
      <div>
        <div class="font-bold text-green-800 leading-tight">{{ $villageSettings->village_name ?: 'Desa Pakel' }}</div>
        <div class="text-xs text-gray-500">{{ $villageSettings->district_name ? 'Kec. '.$villageSettings->district_name : 'Kec. Watulimo' }}, {{ $villageSettings->regency_name ? 'Kab. '.$villageSettings->regency_name : 'Kab. Trenggalek' }}</div>
      </div>
    </a>
    <div class="hidden lg:flex items-center gap-4 xl:gap-6 text-xs xl:text-sm font-medium text-gray-700">
      @foreach ($headerMenu as $item)
        @if ($item['children'] !== [])
          <details class="nav-dropdown relative">
            <summary class="nav-link flex cursor-pointer items-center gap-1 hover:text-green-700">{{ $item['label'] }} <i class="fas fa-chevron-down text-xs" aria-hidden="true"></i></summary>
            <div class="absolute {{ $loop->remaining < 2 ? 'right-0' : 'left-0' }} top-full z-50 mt-3 min-w-52 rounded-xl border border-gray-100 bg-white py-2 text-sm shadow-xl">
              @foreach ($item['children'] as $child)
                <a href="{{ $child['url'] }}" @if ($child['new_tab']) target="_blank" rel="noopener noreferrer" @endif class="block px-4 py-2 hover:bg-green-50 hover:text-green-700">{{ $child['label'] }}</a>
              @endforeach
            </div>
          </details>
        @else
          <a href="{{ $item['url'] }}" @if ($item['new_tab']) target="_blank" rel="noopener noreferrer" @endif class="nav-link hover:text-green-700">{{ $item['label'] }}</a>
        @endif
      @endforeach
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('articles.index') }}#article-search" aria-label="Cari berita" class="flex h-10 w-10 items-center justify-center gap-2 rounded-lg bg-green-700 px-0 text-white hover:bg-green-800 sm:w-auto sm:px-4"><i class="fas fa-search text-sm" aria-hidden="true"></i><span class="hidden sm:inline">Cari</span></a>
      <button id="themeToggle" type="button" class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100" aria-label="Aktifkan mode gelap" title="Aktifkan mode gelap"><i class="fas fa-moon text-xl" aria-hidden="true"></i></button>
      <button id="mobileMenuButton" type="button" class="text-gray-700 lg:hidden" aria-label="Buka menu" aria-controls="mobileMenu" aria-expanded="false"><i class="fas fa-bars text-xl" aria-hidden="true"></i></button>
    </div>
  </div>
  <div id="mobileMenu" class="hidden lg:hidden border-t border-gray-100 py-3 flex flex-col gap-2 text-sm">
    @foreach ($headerMenu as $item)
      @if ($item['children'] !== [])
        <details class="nav-dropdown border-b border-gray-100 pb-2">
          <summary class="flex cursor-pointer items-center justify-between py-2 font-medium">{{ $item['label'] }} <i class="fas fa-chevron-down text-xs" aria-hidden="true"></i></summary>
          <div class="ml-3 flex flex-col gap-1 border-l-2 border-green-100 pl-3 text-gray-600">
            @foreach ($item['children'] as $child)
              <a href="{{ $child['url'] }}" @if ($child['new_tab']) target="_blank" rel="noopener noreferrer" @endif class="py-1">{{ $child['label'] }}</a>
            @endforeach
          </div>
        </details>
      @else
        <a href="{{ $item['url'] }}" @if ($item['new_tab']) target="_blank" rel="noopener noreferrer" @endif class="py-1">{{ $item['label'] }}</a>
      @endif
    @endforeach
  </div>
</div>
</nav>

@push('scripts')
<script>
const themeToggle=document.getElementById('themeToggle');
function setTheme(isDark){
  document.documentElement.classList.toggle('dark-mode',isDark);
  try{localStorage.setItem('pakel-theme',isDark?'dark':'light')}catch(e){}
  themeToggle.setAttribute('aria-label',isDark?'Aktifkan mode terang':'Aktifkan mode gelap');
  themeToggle.title=isDark?'Aktifkan mode terang':'Aktifkan mode gelap';
  themeToggle.innerHTML='<i class="fas fa-'+(isDark?'sun':'moon')+' text-xl" aria-hidden="true"></i>';
  if(window.Chart){
    Chart.defaults.color=isDark?'#cbd5e1':'#666';
    Object.values(Chart.instances||{}).forEach(chart=>{
      chart.options.color=Chart.defaults.color;
      ['x','y'].forEach(axis=>{
        if(chart.options.scales?.[axis]){
          chart.options.scales[axis].ticks={...chart.options.scales[axis].ticks,color:Chart.defaults.color};
          chart.options.scales[axis].grid={...chart.options.scales[axis].grid,color:isDark?'rgba(203,213,225,.15)':'rgba(0,0,0,.1)'};
        }
      });
      chart.update();
    });
  }
}
themeToggle.addEventListener('click',()=>setTheme(!document.documentElement.classList.contains('dark-mode')));
if(document.documentElement.classList.contains('dark-mode')){
  themeToggle.innerHTML='<i class="fas fa-sun text-xl" aria-hidden="true"></i>';
  themeToggle.setAttribute('aria-label','Aktifkan mode terang');
  themeToggle.title='Aktifkan mode terang';
  if(window.Chart) Chart.defaults.color='#cbd5e1';
}

document.querySelectorAll('nav .nav-dropdown > summary').forEach(summary=>{
  summary.addEventListener('click',()=>{
    const currentMenu=summary.parentElement;
    document.querySelectorAll('nav .nav-dropdown[open]').forEach(menu=>{
      if(menu!==currentMenu) menu.open=false;
    });
  });
});

const headerNav=document.querySelector('nav');
const mobileMenu=document.getElementById('mobileMenu');
const mobileMenuButton=document.getElementById('mobileMenuButton');

function setMobileMenuOpen(isOpen){
  if(!mobileMenu || !mobileMenuButton) return;
  mobileMenu.classList.toggle('hidden', !isOpen);
  mobileMenuButton.setAttribute('aria-expanded', String(isOpen));
  mobileMenuButton.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
}

if(mobileMenuButton){
  mobileMenuButton.addEventListener('click', () => {
    setMobileMenuOpen(mobileMenu.classList.contains('hidden'));
  });
}

if(mobileMenu){
  mobileMenu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setMobileMenuOpen(false));
  });
}

document.addEventListener('click',(event)=>{
  headerNav.querySelectorAll('.nav-dropdown[open]').forEach(menu=>{
    if(!menu.contains(event.target)) menu.open=false;
  });
  if(!headerNav.contains(event.target)) setMobileMenuOpen(false);
});

document.addEventListener('keydown',(event)=>{
  if(event.key!=='Escape') return;
  headerNav.querySelectorAll('.nav-dropdown[open]').forEach(menu=>{menu.open=false;});
  setMobileMenuOpen(false);
});
</script>
@endpush
