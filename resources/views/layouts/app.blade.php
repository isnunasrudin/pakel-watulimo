@php
    $villageSettings = app(\App\Services\VillageProfileService::class)->settings();
    $defaultDescription = 'Website resmi Desa Pakel, Kecamatan Watulimo, Kabupaten Trenggalek. Informasi desa, berita, agenda, galeri, dan layanan masyarakat.';
    $pageTitle = trim((string) $__env->yieldContent('title')) ?: 'Desa Pakel, Watulimo - Website Resmi Pemerintah Desa';
    $pageDescription = trim((string) $__env->yieldContent('meta_description')) ?: $defaultDescription;
    $canonicalUrl = trim((string) $__env->yieldContent('canonical')) ?: url()->current();
    $ogTitle = trim((string) $__env->yieldContent('og_title')) ?: $pageTitle;
    $ogImage = trim((string) $__env->yieldContent('og_image')) ?: asset('favicon.ico');
    $ogType = trim((string) $__env->yieldContent('og_type')) ?: 'website';
    $faviconUrl = $villageSettings->faviconUrl();
    $appleTouchIconUrl = $villageSettings->appleTouchIconUrl();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ $faviconUrl }}" sizes="any">
<link rel="apple-touch-icon" href="{{ $appleTouchIconUrl }}">
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="Desa Pakel">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $ogImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>try{if(localStorage.getItem('pakel-theme')==='dark')document.documentElement.classList.add('dark-mode')}catch(e){}</script>
</head>
<body>
    @yield('content')
@stack('scripts')
</body>
</html>
