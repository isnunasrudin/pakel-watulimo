# Website Desa Pakel

Website resmi Desa Pakel, Kecamatan Watulimo, Kabupaten Trenggalek. Aplikasi ini menggabungkan situs publik berbasis Laravel Blade dengan panel admin Filament untuk mengelola seluruh konten desa tanpa mengubah kode.

## Fitur utama

- Beranda dinamis: hero slide, pengumuman, berita, grafik APBDes, statistik desa, cuaca, agenda, galeri, perangkat/pilar desa, dan media sosial.
- Berita dan artikel dengan kategori, pencarian, komentar termoderasi, serta lampiran file.
- Galeri foto per album dengan lightbox, galeri video, dan halaman profil, pemerintahan, layanan, serta kontak.
- Halaman kustom berpola `/halaman/{slug}` dengan rich content, sampul, lampiran, dan pengaturan SEO.
- Panel admin pada path yang dikonfigurasi di `AdminPanelProvider` (tidak dipublikasikan di README) untuk artikel, kategori, halaman, komentar, pengumuman, agenda, slide beranda, album/foto, video, perangkat desa, pilar desa, statistik, program, layanan, media sosial, APBDes, profil desa, menu header, dan pengguna.
- SEO dinamis, `sitemap.xml`, dan `robots.txt`.
- Halaman error 4xx/5xx dengan tampilan yang selaras dengan situs publik, termasuk mode gelap.
- Semua gambar raster yang diunggah diproses menjadi WebP yang dioptimalkan melalui `ImageOptimizer`.

## Stack

- PHP 8.3 dan Laravel 13
- Filament 5 dan `kisame76/filament-advanced-rich-editor`
- Spatie Media Library
- PostgreSQL 17
- Tailwind CSS 4, Vite, dan pnpm
- Chart.js, Swiper, dan Font Awesome

## Kebutuhan sistem

- PHP 8.3 dengan ekstensi `intl`, `gd` atau `imagick`, dan ekstensi standar Laravel.
- Composer dan pnpm.
- PostgreSQL.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database dan kredensial admin pada `.env`, lalu jalankan:

```bash
php artisan migrate --seed
php artisan storage:link
pnpm install
pnpm run build
php artisan serve
```

Akun admin awal dibuat oleh `AdminUserSeeder` dari `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD`. Jika `ADMIN_EMAIL` atau `ADMIN_PASSWORD` kosong, seeder akun admin dilewati.

## Pengembangan

```bash
composer run dev
```

Perintah tersebut menjalankan server Laravel, Vite, queue listener, dan log viewer. Build aset produksi:

```bash
pnpm run build
```

Konfigurasi cuaca opsional tersedia melalui variabel `WEATHER_*` di `.env`. Penyedia bawaan adalah Open-Meteo; widget tetap menampilkan data sementara bila penyedia tidak dapat dihubungi.

## Gambar dan media

- Unggahan gambar baru otomatis dikonversi ke WebP.
- Media library membuat varian WebP untuk sampul, galeri, halaman, slide, serta logo/favicon/hero.
- Untuk memigrasikan gambar lama:

```bash
php artisan images:convert-webp
php artisan media-library:regenerate
```

Gunakan `--dry-run` pada `images:convert-webp` untuk melihat daftar file yang akan diubah.

## Verifikasi

Proyek ini tidak menggunakan pengujian otomatis sebagai alur verifikasi. Pemeriksaan dilakukan secara manual:

```bash
php artisan migrate:status
php artisan route:list --except-vendor
pnpm run build
vendor/bin/pint <path> --format agent
```

Halaman publik dan panel admin diperiksa langsung melalui browser. Rencana pengembangan, keputusan teknis, dan catatan implementasi tersimpan di [`plan.md`](plan.md).
