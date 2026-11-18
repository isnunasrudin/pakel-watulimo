@php
    $status = $status ?? 500;
    $eyebrow = $eyebrow ?? 'Terjadi kesalahan';
    $title = $title ?? 'Permintaan tidak dapat diproses';
    $description = $description ?? 'Silakan kembali ke beranda atau coba beberapa saat lagi.';
    $primaryLabel = $primaryLabel ?? 'Kembali ke beranda';
    $primaryUrl = $primaryUrl ?? url('/');
    $secondaryLabel = $secondaryLabel ?? 'Hubungi kami';
    $secondaryUrl = $secondaryUrl ?? url('/kontak');
    $brandName = config('app.name', 'Desa Pakel');
    $brandInitial = \Illuminate\Support\Str::substr($brandName, 0, 1);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>{{ $status }} — {{ $title }} | {{ $brandName }}</title>
<style>
    :root {
        --site-green-950: #052e16;
        --site-green-900: #14532d;
        --site-green-800: #166534;
        --site-green-700: #15803d;
        --site-amber: #f59e0b;
        --site-surface: #f8fafc;
        --site-card: #ffffff;
        --site-text: #1e293b;
        --site-muted: #64748b;
        --site-border: #e2e8f0;
        --site-soft: #f1f5f9;
        --site-shadow: 0 24px 60px -24px rgba(15, 23, 42, 0.28);
    }

    html.dark-mode {
        --site-green-950: #020c07;
        --site-green-900: #0b1f13;
        --site-green-800: #86efac;
        --site-green-700: #4ade80;
        --site-surface: #0b1220;
        --site-card: #111827;
        --site-text: #e2e8f0;
        --site-muted: #94a3b8;
        --site-border: #334155;
        --site-soft: #1e293b;
        --site-shadow: 0 24px 60px -24px rgba(0, 0, 0, 0.65);
    }

    * { box-sizing: border-box; }

    body {
        min-height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
        background:
            radial-gradient(circle at 12% 8%, rgba(34, 197, 94, 0.13), transparent 30rem),
            radial-gradient(circle at 90% 18%, rgba(245, 158, 11, 0.12), transparent 24rem),
            var(--site-surface);
        color: var(--site-text);
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    a { color: inherit; }

    .site-header {
        width: 100%;
        max-width: 72rem;
        margin: 0 auto;
        padding: 1.25rem 1.25rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .brand {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .brand-mark {
        width: 2.75rem;
        height: 2.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 9999px;
        background: var(--site-green-800);
        color: #fff;
        font-size: 1.125rem;
        font-weight: 800;
    }

    html.dark-mode .brand-mark { color: #052e16; }

    .brand-copy { display: grid; gap: 0.1rem; }
    .brand-name { color: var(--site-green-800); font-size: 1rem; font-weight: 800; line-height: 1.2; }
    .brand-tagline { color: var(--site-muted); font-size: 0.75rem; }

    .theme-toggle {
        width: 2.5rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--site-border);
        border-radius: 0.75rem;
        background: var(--site-card);
        color: var(--site-text);
        cursor: pointer;
        font: inherit;
        transition: background-color 180ms ease, border-color 180ms ease, transform 180ms ease;
    }

    .theme-toggle:hover { background: var(--site-soft); }
    .theme-toggle:active { transform: translateY(1px); }

    .site-main {
        width: 100%;
        max-width: 72rem;
        margin: 0 auto;
        padding: clamp(2.5rem, 7vw, 5.5rem) 1.25rem;
        flex: 1;
        display: flex;
        align-items: center;
    }

    .error-card {
        width: 100%;
        overflow: hidden;
        position: relative;
        border: 1px solid var(--site-border);
        border-radius: 1.5rem;
        background: var(--site-card);
        box-shadow: var(--site-shadow);
        padding: clamp(1.5rem, 5vw, 3.5rem);
    }

    .error-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 0.4rem;
        background: linear-gradient(180deg, var(--site-green-700), var(--site-amber));
    }

    .error-grid {
        display: grid;
        gap: clamp(1.5rem, 5vw, 3rem);
        align-items: center;
    }

    @media (min-width: 768px) {
        .error-grid { grid-template-columns: minmax(0, 1fr) auto; }
    }

    .error-code {
        color: var(--site-green-800);
        font-size: clamp(4.5rem, 15vw, 9rem);
        font-weight: 900;
        letter-spacing: -0.08em;
        line-height: 0.85;
    }

    .error-code span { color: var(--site-amber); }

    .eyebrow {
        display: inline-flex;
        width: fit-content;
        margin: 0 0 0.9rem;
        border-radius: 9999px;
        background: rgba(245, 158, 11, 0.14);
        color: #b45309;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    html.dark-mode .eyebrow { color: #fcd34d; }

    h1 {
        max-width: 44rem;
        margin: 0;
        color: var(--site-text);
        font-size: clamp(1.6rem, 4vw, 2.5rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        text-wrap: balance;
    }

    .description {
        max-width: 40rem;
        margin: 1rem 0 0;
        color: var(--site-muted);
        font-size: 1rem;
        line-height: 1.75;
        text-wrap: pretty;
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.75rem;
    }

    .button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem;
        border-radius: 0.75rem;
        padding: 0.65rem 1.15rem;
        font-size: 0.925rem;
        font-weight: 700;
        text-decoration: none;
        transition: background-color 180ms ease, border-color 180ms ease, color 180ms ease, transform 180ms ease;
    }

    .button:hover { transform: translateY(-1px); }
    .button:active { transform: translateY(0); }

    .button-primary { background: var(--site-green-800); color: #fff; }
    .button-primary:hover { background: var(--site-green-950); }
    html.dark-mode .button-primary { color: #052e16; }
    html.dark-mode .button-primary:hover { background: #bbf7d0; }

    .button-secondary {
        border: 1px solid var(--site-border);
        background: var(--site-card);
        color: var(--site-text);
    }

    .button-secondary:hover { border-color: var(--site-green-700); color: var(--site-green-800); }

    .quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem 1rem;
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--site-border);
        color: var(--site-muted);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .quick-links a:hover { color: var(--site-green-800); }

    .site-footer {
        width: 100%;
        max-width: 72rem;
        margin: 0 auto;
        padding: 0 1.25rem 1.5rem;
        color: var(--site-muted);
        font-size: 0.75rem;
        text-align: center;
    }

    .site-footer a { color: var(--site-green-800); font-weight: 700; text-decoration: none; }
    .site-footer a:hover { text-decoration: underline; }

    a:focus-visible, button:focus-visible {
        outline: 2px solid var(--site-amber);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { transition-duration: 0.01ms !important; }
    }
</style>
<script>
    try {
        if (localStorage.getItem('pakel-theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    } catch (error) {}
</script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="{{ url('/') }}" aria-label="Beranda {{ $brandName }}">
        <span class="brand-mark" aria-hidden="true">{{ $brandInitial }}</span>
        <span class="brand-copy">
            <span class="brand-name">{{ $brandName }}</span>
            <span class="brand-tagline">Website Resmi Pemerintah Desa</span>
        </span>
    </a>
    <button id="themeToggle" class="theme-toggle" type="button" aria-label="Aktifkan mode gelap" title="Aktifkan mode gelap">◐</button>
</header>

<main class="site-main" id="main">
    <section class="error-card" aria-labelledby="error-title">
        <div class="error-grid">
            <div>
                <p class="eyebrow">{{ $eyebrow }}</p>
                <h1 id="error-title">{{ $title }}</h1>
                <p class="description">{{ $description }}</p>

                <div class="actions">
                    <a class="button button-primary" href="{{ $primaryUrl }}">{{ $primaryLabel }}</a>
                    <a class="button button-secondary" href="{{ $secondaryUrl }}">{{ $secondaryLabel }}</a>
                </div>

                <nav class="quick-links" aria-label="Tautan cepat">
                    <a href="{{ url('/berita') }}">Berita</a>
                    <a href="{{ url('/galeri/foto') }}">Galeri Foto</a>
                    <a href="{{ url('/layanan') }}">Layanan</a>
                    <a href="{{ url('/kontak') }}">Kontak</a>
                </nav>
            </div>
            <div class="error-code" aria-hidden="true">{{ $status }}<span>.</span></div>
        </div>
    </section>
</main>

<footer class="site-footer">
    &copy; {{ now()->year }} {{ $brandName }}. <a href="{{ url('/') }}">Kembali ke situs utama</a>
</footer>

<script>
    const themeToggle = document.getElementById('themeToggle');

    function setTheme(isDark) {
        document.documentElement.classList.toggle('dark-mode', isDark);
        themeToggle.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        themeToggle.title = isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
        themeToggle.textContent = isDark ? '☀' : '◐';

        try {
            localStorage.setItem('pakel-theme', isDark ? 'dark' : 'light');
        } catch (error) {}
    }

    const isDark = document.documentElement.classList.contains('dark-mode');
    themeToggle.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    themeToggle.title = isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
    themeToggle.textContent = isDark ? '☀' : '◐';
    themeToggle.addEventListener('click', () => setTheme(!document.documentElement.classList.contains('dark-mode')));
</script>
</body>
</html>
