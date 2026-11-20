@php
    $villageSettings = app(\App\Services\VillageProfileService::class)->settings();
    $siteName = $villageSettings->village_name ?: 'Desa Pakel';
    $defaultDescription = 'Website resmi '.$siteName.', Kecamatan '.($villageSettings->district_name ?: 'Watulimo').', Kabupaten '.($villageSettings->regency_name ?: 'Trenggalek').'. Informasi desa, berita, agenda, galeri, dan layanan masyarakat.';
    $districtName = $villageSettings->district_name ?: 'Watulimo';
    $siteSuffix = $siteName.', '.$districtName;
    $sectionContent = static fn (string $section): string => html_entity_decode(
        trim((string) $__env->yieldContent($section)),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8',
    );

    $titleContent = $sectionContent('title');
    $fullTitle = $sectionContent('full_title');

    if ($fullTitle === '' && $titleContent !== '') {
        $titleContent = preg_replace(
            '/\s*\|\s*'.preg_quote($siteName, '/').'(?:,\s*'.preg_quote($districtName, '/').')?\s*$/iu',
            '',
            $titleContent,
        ) ?? $titleContent;

        $titleContent = trim($titleContent);
        $fullTitle = $titleContent !== '' ? $titleContent.' | '.$siteSuffix : '';
    }

    $pageTitle = $fullTitle ?: 'Website Resmi '.$siteName;
    $pageDescription = \Illuminate\Support\Str::limit(
        $sectionContent('meta_description') ?: $defaultDescription,
        160,
    );
    $canonicalUrl = $sectionContent('canonical') ?: request()->url();
    $currentPage = request()->integer('page');

    if ($currentPage > 1) {
        $canonicalUrl = request()->url().'?page='.$currentPage;
    }

    $ogTitle = $sectionContent('og_title') ?: $pageTitle;
    $ogImage = $sectionContent('og_image')
        ?: ($villageSettings->heroUrl() ?: $villageSettings->iconUrl() ?: asset('favicon.ico'));
    $ogType = $sectionContent('og_type') ?: 'website';
    $robots = request()->filled('q') ? 'noindex, follow' : 'index, follow';
    $faviconUrl = $villageSettings->faviconUrl();
    $appleTouchIconUrl = $villageSettings->appleTouchIconUrl();
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

    $organizationAddress = array_filter([
        '@type' => 'PostalAddress',
        'streetAddress' => $villageSettings->address,
        'addressLocality' => $villageSettings->district_name,
        'addressRegion' => $villageSettings->province_name,
        'addressCountry' => 'ID',
    ], static fn (mixed $value): bool => filled($value));

    $organizationContact = array_filter([
        '@type' => 'ContactPoint',
        'contactType' => 'customer service',
        'telephone' => $villageSettings->phone,
        'email' => $villageSettings->email,
    ], static fn (mixed $value): bool => filled($value));

    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            array_filter([
                '@type' => 'GovernmentOrganization',
                '@id' => url('/').'#organization',
                'name' => $siteName,
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $villageSettings->iconUrl() ?: $faviconUrl,
                ],
                'address' => $organizationAddress,
                'contactPoint' => $organizationContact,
            ], static fn (mixed $value): bool => filled($value)),
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'name' => $siteName,
                'url' => url('/'),
                'inLanguage' => 'id-ID',
                'publisher' => [
                    '@id' => url('/').'#organization',
                ],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('articles.index').'?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ];
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
<meta name="robots" content="{{ $robots }}">
<meta name="theme-color" content="#166534">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:alt" content="{{ $ogTitle }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="{{ $ogTitle }}">

<script type="application/ld+json">{!! json_encode($structuredData, $jsonFlags) !!}</script>
@stack('head')
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>try{if(localStorage.getItem('pakel-theme')==='dark')document.documentElement.classList.add('dark-mode')}catch(e){}</script>
</head>
<body>
    @yield('content')
@stack('scripts')
</body>
</html>
