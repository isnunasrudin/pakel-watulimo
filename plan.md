# Rencana Pengembangan — Website Desa Pakel

Status: Implementasi Fase 0–6 selesai; pengecekan CRUD panel admin (galeri, Fase 3, APBDes, agenda) dan akses admin masih perlu dilakukan manual
Stack: Laravel 13.40 · PHP 8.3 · Filament 5.11 · kisame76/filament-advanced-rich-editor 1.9.0 · Filament Spatie Media Library plugin 5 · spatie/laravel-medialibrary 11 · Tailwind v4 + Vite · pnpm · PostgreSQL 17 (dev & produksi)

Kebijakan verifikasi: **tanpa test otomatis** (instruksi pemilik proyek). Setiap perubahan diverifikasi lewat migrasi, `pnpm run build`, Pint, dan pengecekan manual halaman/route.

---

## 1. Tujuan

Mengubah website Desa Pakel dari **prototipe statis** (semua konten hardcoded di Blade/PHP) menjadi **aplikasi berbasis database** yang isinya dikelola lewat Filament admin panel di `/admin`.

Hasil akhir yang diharapkan:

- Perangkat desa dapat menulis berita, mengumumkan informasi, mengunggah foto/video, dan memperbarui data desa tanpa mengubah kode.
- Semua halaman publik mengambil data dari database, bukan konstanta.
- Ada alur verifikasi manual yang konsisten untuk route utama dan CRUD admin.
- Tailwind ter-build lewat Vite (bukan CDN), sehingga styling konsisten dan aman untuk produksi.

## 2. Kondisi Awal (sebelum PR 1)

| Area | Kondisi | Dampak |
|---|---|---|
| Domain data | Belum ada model/migrasi selain `users`, `cache`, `jobs` | Semua konten tidak bisa dikelola |
| Konten | Hardcoded di `partials/home/*` dan `partials/widgets/*` | Setiap perubahan konten = deploy kode |
| Galeri | `GalleryController::ALBUMS` berupa `private const` | Album/foto tidak bisa ditambah dari UI |
| Admin | Panel Filament kosong (hanya `Dashboard`, `AccountWidget`, `FilamentInfoWidget`) | Belum ada Resource sama sekali |
| CSS | Layout memuat `cdn.tailwindcss.com`, asset Vite tidak dipakai | Build pipeline tidak berfungsi, tidak produksi-ready |
| Aset | `assets/pakel-semarak-dummy.png` tidak ada di `public/` | Logo hero broken |
| Auth | Seeder hanya membuat `test@example.com` | Perlu akun admin + kontrol akses panel |
| Test | Hanya 2 contoh bawaan | Tidak ada jaring pengaman untuk regresi |

## 3. Ruang Lingkup

**Termasuk**

- Skema database + model + factory + seeder untuk konten desa.
- Filament Resource untuk seluruh konten tersebut (CRUD + upload gambar).
- Migrasi Tailwind CDN → Vite build dan bundling seluruh aset frontend (Chart.js, Swiper, Font Awesome, font Inter) dengan pnpm.
- Pindah koneksi database ke PostgreSQL untuk dev dan produksi.
- Komentar artikel persisten dengan moderasi di Filament.
- Refactor halaman publik dan widget agar membaca dari database.

**Tidak termasuk (tanpa persetujuan terpisah)**

- Menambah dependency baru tanpa persetujuan. `kisame76/filament-advanced-rich-editor` 1.9.0 telah disetujui untuk menggantikan UI RichEditor artikel; `spatie/laravel-medialibrary` + plugin Filament tetap digunakan untuk gambar sampul (§9.11).
- Integrasi API cuaca nyata berbayar (pakai cache + data contoh dulu).
- Bundling CDN frontend ke Vite sudah dilakukan di Fase 0 (tidak lagi relevan sebagai pengecualian).
- Form kontak berbasis database/email (disepakati: cukup tautan `mailto:`/`wa.me`).
- Rebranding / redesign visual penuh.

## 4. Arsitektur Target

### 4.1 Skema Database

Semua tabel memakai `id`, `timestamps`, dan `slug` unik bila dipakai di URL. Kolom `status`/boolean untuk publikasi.

**Konten berita**
- `categories` — `name`, `slug`, `description`, `color`, `order`
- `articles` — `user_id` (penulis), `category_id`, `title`, `slug`, `excerpt`, `body`, `cover_image`, `is_pinned`, `is_featured`, `status` (`draft|published`), `published_at`, `views_count`, `meta_title`, `meta_description`, plus media `attachments` (PDF/Word/Excel/PowerPoint/TXT/CSV)
- `announcements` — `title`, `body`, `level` (`info|warning|urgent`), `link_label`, `link_url`, `starts_at`, `ends_at`, `is_active`
- `pages` — `title`, `slug`, `excerpt`, `content` (rich HTML), `meta_title`, `meta_description`, `is_published` (halaman kustom di `/halaman/{slug}`), plus media `attachments` (PDF/Word/Excel/PowerPoint/TXT/CSV)

**Agenda & pemerintahan**
- `agendas` — `title`, `slug`, `description`, `location`, `starts_at`, `ends_at`, `is_published`
- `village_officials` — `name`, `position`, `photo`, `background_color`, `phone`, `order` (kolom `period` lama dibiarkan di database agar data tidak terhapus, tetapi tidak dipakai lagi)
- `village_pillars` — `name`, `position`, `photo`, `background_color`, `phone`, `order` (Pilar Desa: Babinsa, bidan desa, dan mitra pemerintahan)

**Galeri**
- `albums` — `title`, `slug`, `description`, `cover_photo_id` (nullable), `is_published`, `order`
- `photos` — `album_id`, `path`, `caption`, `taken_at`, `order`
- `videos` — `title`, `slug`, `video_url`, `thumbnail`, `description`, `published_at`

**Data & sinergi desa**
- `village_statistics` — `label`, `value`, `unit`, `icon`, `group`, `order`
- `program_links` — `title` (label internal/alt), `description`, `icon`, `url`, `background_color`, `order`, plus media `image` (widget "Sinergi Program" tampil sebagai gambar penuh)
- `service_links` — `title`, `description`, `icon`, `url`, `order` (menu Layanan)
- `social_links` — `platform`, `url`, `icon`, `order`

**Keuangan (APBDes)**
- `budget_items` — `year`, `section` (`pendapatan|belanja|pembiayaan`), `group` (mis. "Dana Desa"), `label`, `budget_amount` (bigint), `realization_amount` (bigint), `order`
- Agregat grafik dihitung dari `budget_items` (tidak disimpan terpisah).

**Interaksi publik**
- `article_comments` — `article_id`, `name`, `email` (nullable), `body`, `status` (`pending|approved|rejected`), `ip_address`, `user_agent`, `timestamps`. Hanya status `approved` yang tampil; moderasi lewat `CommentResource`.

**Masih terbuka**
- `settings` (nama desa, alamat, kontak, embed peta) — tabel manual vs paket settings.
- `contact_messages` — tidak dibuat (keputusan §9: cukup tautan email/WhatsApp).

### 4.2 Lapisan Aplikasi

```
app/
  Models/                          # Article, Category, Album, Photo, ...
  Http/Controllers/
    HomeController.php             # agregasi data beranda (eager load, cache)
    ArticleController.php          # index, show, byCategory
    GalleryController.php          # index, show(album), videos
    PageController.php             # profil, pemerintahan, layanan, kontak
  Http/Requests/                   # bila ada form publik
  Filament/Resources/              # 1 Resource per model konten
  Filament/Resources/*/RelationManagers/PhotosRelationManager.php
  Filament/Widgets/                # statistik ringkas dashboard
  Policies/                        # hanya bila perlu aturan per-role
```

**Konvensi wajib** (dari `AGENTS.md`): buat file via `php artisan make:*  --no-interaction`, return type + type hint eksplisit, PHP 8 constructor promotion, PHPDoc array shape, jalankan `vendor/bin/pint --dirty --format agent` setiap selesai.

### 4.3 Rute Target

| Method | URI | Name | Keterangan |
|---|---|---|---|
| GET | `/` | `home` | Beranda (tetap) |
| GET | `/berita` | `articles.index` | Daftar berita + pagination |
| GET | `/berita/{article:slug}` | `articles.show` | Detail artikel |
| GET | `/kategori/{category:slug}` | `categories.show` | Filter kategori |
| GET | `/galeri/foto` | `gallery.photos` | Daftar album (tetap, nama route dipertahankan) |
| GET | `/galeri/foto/{album:slug}` | `gallery.album` | Album (URL shareable, menggantikan hash JS) |
| GET | `/galeri/video` | `gallery.videos` | Daftar video |
| GET | `/profil`, `/pemerintahan`, `/layanan`, `/kontak` | `pages.*` | Halaman statis dari DB/settings |
| GET | `/halaman/{page:slug}` | `pages.show` | Halaman kustom terbit (rich content) |
| — | path panel (rahasia) | `filament.admin.*` | Panel (path dikonfigurasi di `AdminPanelProvider`) |

Kompatibilitas: rute statis `berita.contoh-artikel` dihapus setelah artikel pindah ke DB; URL lama menjadi 404 (keputusan §9.2). Tautan internal diperbarui ke `route('articles.show', $article)` sehingga tidak ada link mati di UI.

## 5. Fase Pengerjaan

Setiap fase harus selesai dengan: migrasi jalan, `vendor/bin/pint --format agent` bersih, dan pengecekan manual halaman terkait. (Opsi `--dirty` hanya jalan bila folder ini sudah menjadi Git repo.)

### Fase 0 — Fondasi (prasyarat) — SELESAI

- [x] Konfigurasi PostgreSQL: `.env` (pgsql, `pakel-watulimo`), `.env.example` diperbarui, database test `pakel-watulimo-test` dibuat, `phpunit.xml` memakai pgsql + database test.
- [x] Buat akun admin: kolom `is_admin` boolean pada `users` + gate akses panel.
- [x] Implementasikan `FilamentUser::canAccessPanel()` agar hanya admin bisa masuk `/admin`; user non-admin mendapat 403.
- [x] Sembunyikan slot logo hero (blok `#heroLogoSlot` dihapus) sampai logo final tersedia.
- [x] Pindahkan Tailwind dari CDN ke Vite: `@vite([...])` di layout, seluruh CSS kustom dipindah ke `resources/css/app.css`, `pnpm run build` sukses.
- [x] Bundel seluruh aset frontend via Vite + pnpm: Chart.js, chartjs-plugin-datalabels, Swiper, Font Awesome; font Inter diambil lewat plugin Bunny (tanpa Google Fonts). Semua referensi CDN di layout dihapus.
- [x] Sesuaikan inline script yang bergantung pada global (`Swiper`, `Chart`) agar berjalan di `DOMContentLoaded`, karena modul Vite bersifat deferred.
- [x] Ganti package manager ke pnpm (`pnpm-lock.yaml`), `php artisan dev` otomatis memakai `pnpm run dev`.
- [x] Tambah `UserFactory::admin()` dan `AdminUserSeeder` yang membaca `config('desa.admin.*')` dari `.env`.
- [x] Feature test `AdminAccessTest` dibuat sebelum kebijakan tanpa-test berlaku. Saat ini tidak dijalankan dan tidak menjadi bagian alur verifikasi.

**Catatan implementasi:**
- `canAccessPanel()` mengembalikan `(bool) $this->is_admin` karena atribut bisa `null` pada model yang baru dibuat sebelum di-refresh dari database.
- `is_admin` sengaja **tidak** masuk `#[Fillable]`; factory tetap bisa mengisinya karena Eloquent factory memakai `Model::unguarded`.
- Kredensial admin awal: lihat `ADMIN_EMAIL`/`ADMIN_PASSWORD` di `.env`. Seeder memakai `firstOrCreate` agar seed ulang tidak mereset password.
- Style: Pint dijalankan per-path karena `--dirty` butuh repo Git.
- Build produksi: `pnpm run build`; development: `pnpm run dev` atau `composer run dev`.
- Dependensi frontend kini: `chart.js`, `chartjs-plugin-datalabels`, `swiper`, `@fortawesome/fontawesome-free`.

**Selesai bila:** login `/admin` berhasil dengan akun admin, user biasa ditolak, aplikasi jalan di PostgreSQL, dan halaman publik tampil normal tanpa CDN eksternal.

### Fase 1 — Berita & Pengumuman — SELESAI

- [x] Migrasi + model + factory: `categories`, `articles`, `announcements`, `article_comments`.
- [x] `ArticleResource` (judul → slug otomatis, kategori, ringkasan, body Advanced Rich Editor, upload sampul, pinned/featured, status, tanggal terbit) + filter status/kategori + pencarian + aksi "Lihat".
- [x] `CommentsRelationManager` di `ArticleResource` (setujui/tolak/edit/hapus).
- [x] `ArticleCommentResource` global (filter status, aksi massal setujui) + form komentar publik dengan honeypot `website`, `throttle:5,1`, dan moderasi.
- [x] `CategoryResource` dan `AnnouncementResource` (label, grup navigasi "Konten", filter).
- [x] Seeder memindahkan 8 artikel, 9 kategori, 1 pengumuman, dan 3 komentar contoh dari data hardcoded.
- [x] `HomeController` mengambil 8 artikel terbaru (pinned dulu) + pengumuman aktif; eager load kategori/penulis.
- [x] `ArticleController` (index dengan pencarian + pagination, show dengan penambah view dan artikel terkait), `CategoryController`, `CommentController`.
- [x] Rute baru: `/berita`, `/berita/{slug}`, `POST /berita/{slug}/komentar`, `/kategori/{slug}`.
- [x] View baru `pages/articles/index.blade.php`, `pages/articles/show.blade.php`, partial `partials/articles/card.blade.php`; `main-content` dan `announcement` sekarang loop data.
- [x] Rute `berita.contoh-artikel` dan `pages/article.blade.php` dihapus; URL lama 404.

**Catatan implementasi:**
- Body artikel tetap disimpan sebagai HTML dan dirender dengan `$article->renderRichContent('body')`. Form memakai `kisame76/filament-advanced-rich-editor` 1.9.0 dengan preset `blog`; `AdvancedRichContentRenderer::bind()` membuat fitur renderer paket tersedia di halaman publik. Format konten yang tersimpan tidak memerlukan migrasi.
- Lampiran gambar isi artikel disimpan pada disk `public`, folder `articles/content`, bukan di koleksi Spatie `body`. Tipe yang diterima JPEG/PNG/WebP dengan batas 4 MB; media browser editor dibatasi ke gambar pada folder tersebut. `Article::setUpRichContent()` menunjuk renderer ke disk yang sama agar path lampiran dapat di-resolve.
- Gambar isi mendukung alt text, caption, crop, rotasi, dan pengubahan ukuran piksel. Tinggi editor dapat diubah dengan drag vertikal; batasnya 12rem–70vh. `config/livewire.php` menaikkan `payload.max_nesting_depth` menjadi 32 sesuai kebutuhan dokumen bertingkat.
- Gambar sampul tetap memakai Spatie Media Library (collection `cover`, single file, konversi `thumb` 480×270 dan `card` 800×450). Tanpa sampul dipakai placeholder `picsum.photos` berbasis slug.
- Komentar baru berstatus `pending`; hanya `approved` yang tampil di halaman publik.
- Slug tidak diubah otomatis saat edit untuk menjaga URL artikel yang sudah dibagikan.

**Selesai bila:** artikel baru dari `/admin` langsung muncul di beranda dan `/berita/{slug}`; artikel draft tidak tampil. **Verifikasi manual:** `/`, `/berita`, `/berita/{slug}`, `/kategori/{slug}` → 200; `/berita/contoh-artikel` → 404; komentar pending tidak tampil dan tersimpan dengan status `pending`.

### Fase 2 — Galeri (Foto & Video)

- [x] Migrasi + model + factory: `albums`, `photos`, `videos`.
- [x] `AlbumResource` + `PhotosRelationManager` (unggah beberapa foto, caption, urutan, `taken_at`), `VideoResource`.
- [x] Refactor `GalleryController`: hapus `const ALBUMS`, query album terbit dengan `withCount('photos')` dan cover.
- [x] Album memiliki halaman/URL sendiri di `/galeri/foto/{album:slug}`; navigasi hash lama dihapus.
- [x] Halaman `/galeri/video` mengambil data dari tabel `videos`.
- [x] Widget `galeri-foto` dan `galeri-video` di beranda mengambil data terbaru dari database.
- [x] Foto dan thumbnail disimpan pada disk `public`; `public/storage` sudah terhubung melalui `storage:link`.

**Catatan implementasi:**
- `GallerySeeder` memindahkan 4 album dan 12 foto demo dari data hardcoded. URL Picsum hanya digunakan untuk gambar demo; unggahan baru disimpan di `gallery/photos`.
- Foto dapat diunggah satu per satu atau sekaligus (maksimal 30 foto per aksi). Caption, tanggal foto, urutan, dan pemilihan sampul dikelola di relation manager album. Foto yang sedang menjadi sampul ditandai badge **Sampul aktif**, dan aksi **Jadikan sampul** berubah menjadi **Sampul aktif** serta dinonaktifkan.
- Foto album baru memakai Spatie Media Library: model `Photo` mengimplementasikan `HasMedia` dengan koleksi `photo`. Form **Tambah satu foto** memakai `SpatieMediaLibraryFileUpload`; unggah massal memakai `FileUpload` sementara lalu menyimpan berkas ke koleksi media yang sama. Kolom `photos.path` tetap dipertahankan sebagai fallback untuk data demo/URL eksternal.
- Foto yang diunggah dihapus dari storage saat record dihapus; sampul album berpindah ke foto berikutnya atau dikosongkan.
- Lightbox album memakai `<dialog>` dengan `margin:auto`, backdrop gelap + blur, scroll body dikunci saat terbuka, dan fokus kembali ke foto asal saat ditutup.
- Video memakai `published_at` untuk menentukan publikasi. Tidak ada video demo yang diseed; halaman dan widget menampilkan empty state sampai video ditambahkan dari panel. Thumbnail disimpan di `gallery/videos`.
- URL manual: `/`, `/galeri/foto`, `/galeri/foto/kegiatan-warga`, dan `/galeri/video` merespons 200. Test otomatis tidak dijalankan sesuai kebijakan proyek.
- **Masih perlu:** masuk ke `/admin`, verifikasi unggah foto tunggal/multiple dan CRUD video secara manual.

**Selesai bila:** album dan foto bisa dikelola dari panel, album terbit tampil di publik, gambar terupload dapat diakses.

### Fase 3 — Profil, Pemerintahan, Statistik, Program

- [x] Migrasi + model: `village_officials`, `village_statistics`, `program_links`, `service_links`, `social_links`.
- [x] Resource Filament untuk tiap model (dengan `order` via drag atau numeric input).
- [x] Ganti `partials/home/statistics.blade.php` dan widget `perangkat-desa`, `sinergi-program`, `media-sosial`, `statistik-pengunjung` agar membaca DB.
- [x] Halaman `/profil`, `/pemerintahan`, `/layanan`, `/kontak` memakai data yang sama (hindari duplikasi konten hardcoded).
- [x] Footer `#kontak`: ganti tautan `#` dengan `mailto:`, `wa.me`, tautan peta, dan URL sosmed yang dapat dikonfigurasi (tanpa form kontak).
- [x] Cache hasil agregasi beranda (mis. `Cache::remember` 5–10 menit) + invalidasi saat Resource disimpan/dihapus.

**Catatan implementasi:**
- Seluruh model Fase 3 sudah memiliki migrasi, factory, dan Resource Filament. Kolom urutan memakai `TextColumn` read-only; pengurutan dilakukan lewat drag-and-drop (kecuali APBDes yang grouped) dan tombol Naik/Turun.
- `village_settings` adalah singleton: hanya bisa dibuat jika belum ada, tidak bisa dihapus, dan dipakai navbar/footer serta halaman `/profil` dan `/kontak`. Profil desa juga menyimpan identitas visual (`icon`/logo, `favicon`, dan gambar hero) melalui Spatie Media Library; navbar, footer, `<head>` publik, dan background hero memakai media tersebut dengan fallback. Gambar hero manual mengalahkan cover album pertama; jika kosong, hero memakai cover album pertama. Favicon panel admin juga mengikuti favicon yang diunggah. Semua koleksi media memakai disk `public` (`useDisk('public')` + `SpatieMediaLibraryFileUpload::disk('public')`); media icon/favicon lama dipindahkan dari `local` ke `public`.
- Copywriting utama beranda dapat diubah di **Pengaturan → Profil Desa** melalui bagian **Copywriting hero beranda** (badge, judul, baris penekanan, deskripsi, label/tautan dua tombol) dan **Judul bagian beranda** (berita + label tautan, APBDes, statistik). Kolom nullable disimpan di `village_settings`; teks kosong memakai fallback (judul → nama desa, penekanan → kecamatan, deskripsi → deskripsi profil, heading → teks bawaan). Tautan tombol divalidasi `SafeUrl` (HTTP(S), path internal, atau anchor) dan disanitasi lagi lewat `VillageSetting::homeCopy()`.
- Widget `statistik-pengunjung` membaca tabel `sessions` (IP unik hari ini dan `session.lifetime` menit terakhir) serta `articles.views_count` untuk total pembaca. Tidak ada tabel baru; metrik ini adalah versi awal yang dapat diperluas pada Fase 5.
- Halaman `/profil`, `/pemerintahan`, `/layanan`, dan `/kontak` memakai model yang sama dengan beranda (`VillageSetting`, `VillageStatistic`, `VillageOfficial`, `ServiceLink`, `SocialLink`); tidak ada lagi data perangkat/statistik/program yang hardcoded di Blade.
- Footer `#kontak` memakai `mailto:`, tautan WhatsApp, peta/embed, dan media sosial dari `village_settings`/`social_links`; tautan `#` placeholder sudah tidak ada.
- Cache `home.village-content` (10 menit) diinvalidasi oleh event `saved`/`deleted` pada `VillageOfficial`, `VillageStatistic`, `ProgramLink`, `ServiceLink`, dan `SocialLink`. Cache footer `public.service-links`/`public.social-links` diinvalidasi oleh model terkait, sedangkan `village.settings.current` oleh `VillageSetting`.
- Data demo `social_links` dan kolom kontak (`phone`, `email`, `whatsapp`, `maps_url`, `map_embed_url`) masih kosong; isi lewat panel agar tautan footer/widget muncul. Widget menampilkan empty state selama data kosong.
- Foto perangkat desa mempertahankan rasio asli gambar (`h-auto w-full`) di halaman pemerintahan dan widget beranda; bingkai `3:4`/`object-cover` dihapus. Widget memakai Swiper dua kolom di mobile dengan `autoHeight`, dan aturan global `max-height: 240px` dikecualikan khusus `.perangkatSwiper`. Editor admin tidak memaksa rasio crop; crop hanya manual bila diperlukan, dan area transparan mengikuti warna latar yang dipilih.
- Dashboard admin diisi widget ringkasan: statistik artikel, agenda mendatang, galeri, komentar pending, dan pengguna; tabel **Artikel Terbaru**, **Agenda Mendatang**, dan **Komentar Menunggu Moderasi** dengan tautan langsung ke edit/create. Widget dirender non-lazy agar data langsung terlihat, dan widget bawaan Account/Filament Info dihapus.
- **Pengguna** dikelola lewat **Pengaturan → Pengguna** (`UserResource`): nama, email unik, password/konfirmasi opsional saat edit, dan status Administrator. Akun administrator bisa membuat pengguna baru; akun sendiri tidak bisa dihapus atau diturunkan statusnya dari form. Password disimpan melalui cast `hashed`, dan hanya pengguna `is_admin` yang dapat masuk panel.
- **Pilar Desa** dikelola lewat **Profil & Pemerintahan → Pilar Desa** (`village_pillars`) dengan struktur yang sama seperti Perangkat Desa: nama, jabatan, foto WebP, warna latar transparan, telepon, dan urutan. Data tampil sebagai bagian **Pilar Desa** di halaman `/pemerintahan` (anchor `#pilar-desa`) dan sebagai widget slider dua kolom dengan lightbox di beranda. Belum ada data seed; admin mengisi sendiri (mis. Babinsa, Bidan Desa).
- Widget Perangkat Desa memakai Swiper dua kolom di semua ukuran layar (termasuk mobile). Setiap foto adalah tombol dengan overlay ikon perbesar; klik membuka dialog pratinjau penuh dengan caption nama/jabatan, backdrop gelap, scroll lock, pengembalian fokus, dan jeda autoplay selama pratinjau terbuka.
- Gambar transparan pada Sinergi Program dan Perangkat Desa memakai pilihan warna latar dari palet `ImageBackgroundPalette` (26 warna dengan swatch, termasuk warna netral, pastel, dan warna berani/tegas; default abu terang `#f3f4f6`). Pilihan disimpan di `program_links.background_color` dan `village_officials.background_color`, ditampilkan sebagai inline style di widget, halaman pemerintahan, dan thumbnail tabel admin; nilai divalidasi ulang lewat fallback palet.
- Verifikasi manual (tanpa test otomatis): `migrate:status` bersih, `route:list` memuat route halaman dan Resource terkait, `pnpm run build` sukses, Pint bersih, serta `/`, `/profil`, `/pemerintahan`, `/layanan`, dan `/kontak` merespons 200 dengan nilai statistik/perangkat/program dari database. Icon/favicon diuji dengan media sementara (navbar/footer dan `<link rel="icon">` berubah), lalu dibersihkan. Copywriting beranda diuji lewat render partial dengan model in-memory (nilai kustom muncul) dan `SafeUrl` menolak `javascript:`; halaman `/` tetap 200 dengan teks bawaan. CRUD panel belum dicek lewat login manual.

**Selesai bila:** angka statistik dan daftar perangkat desa berubah dari panel tanpa deploy.

### Fase 4 — Keuangan APBDes

- [x] Migrasi + model + factory: `budget_items`.
- [x] `BudgetItemResource` dengan filter tahun/section, input rupiah terformat, validasi angka.
- [x] Endpoint/partial chart: hitung agregat per section dan per group, kirim sebagai JSON ke Chart.js (hilangkan angka hardcoded di `finance.blade.php`).
- [x] Tampilkan total pendapatan/belanja/pembiayaan + persentase realisasi.
- [x] Pemilih tahun APBDes di beranda (`?tahun=YYYY`) dengan opsi dari tahun yang tersedia di `budget_items`.
- [x] Grouping tabel Resource APBDes per Bagian (default) dan per Tahun.

**Catatan implementasi:**
- Tabel `budget_items` menyimpan `year`, `section` (`pendapatan|belanja|pembiayaan`), `group`, `label`, `budget_amount`, `realization_amount`, dan `order`; nilai rupiah disimpan sebagai bigint tanpa desimal.
- `BudgetItemResource` berada di grup navigasi "Keuangan", mendukung filter tahun/section, input rupiah bermasker (`$money`), validasi numerik/min 0, serta kolom persentase realisasi.
- `HomeController::budgetSummary()` mengambil tahun dari query `tahun` (fallback ke tahun terbaru), menghitung total per section dan agregat per `group`, lalu mengirim seluruh data sebagai JSON ke Chart.js di `partials/home/finance.blade.php`. Tidak ada angka APBDes hardcoded lagi.
- Kartu ringkasan menampilkan realisasi, anggaran, dan persentase untuk Pendapatan/Belanja/Pembiayaan; chart pelaksanaan memakai total section, chart pendapatan/belanja memakai agregat group.
- `BudgetSeeder` menyediakan 12 item demo tahun 2026 yang mereproduksi total data lama (Pendapatan Rp 1.458.325.800 / Rp 450.550.322; Belanja Rp 1.597.025.800 / Rp 67.032.649; Pembiayaan Rp 138.700.000 / Rp 184.149.708). Realisasi desimal lama dibulatkan ke rupiah penuh.
- `DatabaseSeeder` memanggil `BudgetSeeder`; pemanggilan `VillageDataSeeder` yang ganda dihapus.
- Tabel admin dikelompokkan per Bagian (default: Pendapatan → Belanja → Pembiayaan) dan dapat diganti ke Tahun; grup dapat dilipat. Opsi grup disediakan lewat `Group::make('section')` dan `Group::make('year')`.
- Halaman publik menampilkan dropdown tahun bila ada data; pilihan dikirim sebagai `?tahun=YYYY` dan divalidasi terhadap tahun yang tersedia, dengan fallback ke tahun terbaru.
- Verifikasi manual (tanpa test otomatis): migrasi batch 2 berhasil, data seed 12 baris, `route:list` memuat tiga route Resource APBDes, `pnpm run build` sukses, Pint bersih, `/` dan `/?tahun=2026` merespons 200 serta menampilkan kartu + JSON chart dari DB. Dropdown multi-tahun diuji sementara dengan satu baris 2025 (lalu dihapus); grup tabel diverifikasi lewat judul dan SQL urutan. `/admin/budget-items` mengembalikan 302 ke login (sesuai belum login). CRUD panel belum dicek lewat login manual.

**Selesai bila:** grafik berubah sesuai data di panel; angka di kartu dan chart konsisten dengan tabel.

### Fase 5 — Widget Eksternal & Halaman Pendukung

- [x] `prakiraan-cuaca`: service kecil + `Http::` + `Cache::remember` (24 jam), fallback statis bila API gagal, API key dari `config/services.php` + `.env`.
- [x] `statistik-pengunjung`: hitung dari tabel `sessions` atau counter tersimpan (putuskan metriknya).
- [x] `arsip-artikel` dan `kategori-artikel`: query agregat dari `articles`.
- [x] `agenda-desa`: dari tabel `agendas`, tampilkan yang akan datang, arsip yang lewat.

**Catatan implementasi:**
- `App\Services\WeatherService` memakai `Http::timeout(...)->get(...)` ke Open-Meteo, cache 24 jam untuk hasil sukses, dan fallback statis 10 menit bila penyedia gagal. Data widget dirender server-side, bukan fetch browser. Konfigurasi ada di `config/services.php` (`services.weather`) dan variabel `WEATHER_*` di `.env.example`; API key bersifat opsional karena Open-Meteo tidak mewajibkannya.
- `statistik-pengunjung` sudah diimplementasikan pada Fase 3: IP unik hari ini, pengunjung aktif berdasarkan `session.lifetime`, dan total `articles.views_count`.
- `kategori-artikel` memakai `withCount` artikel terbit + `whereHas`, lalu menautkan ke `/kategori/{slug}`. `arsip-artikel` mengagregasi `published_at` per bulan (maksimal 6 bulan) dari artikel terbit.
- `agendas` memiliki migrasi, model, factory, Resource Filament (grup navigasi Konten), dan `AgendaSeeder` berisi 6 event demo relatif terhadap tanggal seed. Widget membagi event menjadi sudah berlalu/sedang/akan datang, memilih tab aktif secara otomatis, serta menampilkan lokasi dan rentang tanggal.
- `HomeController` mempartisi agenda di memori setelah satu query; tidak ada data agenda, arsip, atau kategori yang hardcoded di Blade.
- Verifikasi manual (tanpa test otomatis): migrasi batch 3 (`agendas`) berhasil, 6 agenda ter-seed, `/` merespons 200 dengan cuaca live + forecast 4 hari, 2 arsip bulan, 7 kategori berartikel, dan tab agenda terisi. Fallback cuaca diuji dengan base URL tidak valid dan menghasilkan 4 hari data statis. `route:list` memuat 3 route Resource Agenda, `pnpm run build` + Pint bersih, serta `/admin/agendas` mengembalikan 302 ke login. CRUD panel belum dicek lewat login manual.

**Selesai bila:** tidak ada lagi data palsu yang tidak berasal dari DB/konfigurasi, dan widget tetap tampil saat sumber eksternal gagal.

### Fase 6 — Pengerasan & Kualitas

- [x] Audit N+1 dengan `DB::listen`/`preventLazyLoading()` di lokal.
- [x] SEO: sitemap.xml, `robots.txt`, meta dinamis per halaman, canonical memakai `APP_URL`.
- [x] Aksesibilitas: cek kontras dark mode, `aria-*`, fokus keyboard pada menu & galeri.
- [x] Hapus route/view yang sudah tidak dipakai (`pages/article.blade.php` statis) setelah migrasi.
- [x] Bersihkan `public/documents/*.txt` atau hubungkan ke halaman terkait.

**Catatan implementasi:**
- Diagnostik lokal di `AppServiceProvider`: `Model::preventLazyLoading()` aktif untuk halaman publik non-admin, dan `DB::listen` mencatat query ≥ 500 ms ke log. Query artikel terkait di halaman detail sekarang eager-load `category` dan `author`.
- Profil desa diakses melalui `VillageProfileService` yang di-scope per request. Layout, view composer navbar/footer, controller halaman, dan favicon panel memakai instance yang sama, sehingga `VillageSetting::current()` beserta pemuatan media identitas hanya dijalankan sekali per request.
- Semua field boolean/toggle di Resource (`is_published`, `is_active`, `is_pinned`, `is_featured`) ditampilkan sebagai `ToggleColumn` yang dapat diubah langsung dari tabel. Status non-boolean tetap memakai badge (artikel, komentar, video, agenda).
- Validasi URL kustom pada form Filament memakai rule object `App\Rules\SafeUrl`; closure `$attribute, $value, $fail` tidak dipakai karena Filament mengevaluasi closure sebagai dependency injection dan menghasilkan `BindingResolutionException`.
- Kolom `order` pada Album, Foto album, Kategori, Program, Layanan, Media Sosial, Perangkat Desa, Statistik Desa, APBDes, dan Menu Header ditampilkan sebagai `TextColumn` read-only; pengurutan dilakukan melalui drag-and-drop atau tombol Naik/Turun.
- Tabel ber-`order` memakai `reorderable('order')` untuk drag-and-drop, ditambah aksi **Naik/Turun** icon-only melalui trait `InteractsWithRecordOrder`. APBDes yang dikelompokkan per bagian memakai up/down per tahun+section tanpa drag agar urutan antar-bagian tidak tercampur.
- SEO dipindahkan ke `layouts/app.blade.php` memakai section `title`, `meta_description`, `canonical`, `og_title`, `og_image`, dan `og_type`; canonical/OG memakai URL generator Laravel (APP_URL/request host), bukan domain hardcoded. `SitemapController` menyajikan `/sitemap.xml` (halaman statis, halaman kustom terbit, artikel, kategori, album) dan `/robots.txt` dinamis; `public/robots.txt` statis dihapus.
- Aksesibilitas: style `:focus-visible` global, `aria-expanded` pada tombol menu mobile, link pencarian yang berfungsi, dialog galeri dengan `aria-describedby` + pengembalian fokus, dukungan `prefers-reduced-motion` untuk animasi dekoratif (Swiper dikecualikan agar transisi slider tetap halus seperti referensi), serta kontras dark mode untuk teks hijau/kuning/biru/merah.
- Slider “Selayang Pandang” memakai entitas `home_slides` (Resource **Konten → Slide Beranda**: sumber, judul, deskripsi, media gambar, tautan, urutan, aktif) dengan tiga slide default sesuai `index.html`; fallback ke album jika kosong. Sumber slide bersifat polymorphic: upload manual, foto galeri (`photo_id`), atau artikel (`article_id`); judul/deskripsi otomatis memakai data sumber bila field override kosong, dan tautan otomatis mengarah ke album/artikel atau memakai `link_url` kustom. CSS Swiper diimpor di `app.css` **sebelum** aturan kustom agar tombol tidak tertimpa (34px, ikon 16px, offset 10px), `swiper/css/effect-fade` disertakan, dan hero memakai background album + logo settings pada grid dua kolom.
- Cleanup: route `/test`, `resources/views/test.blade.php`, `contoh-jpeg-sebagai-txt.txt`, `contoh-png-sebagai-txt.txt`, `public/documents/*.txt`, dan `public/robots.txt` statis dihapus. Komentar HTML/Blade pada seluruh template view juga dihapus; komentar dokumentasi PHP dan fitur komentar artikel tetap dipertahankan.
- Halaman error 4xx/5xx memakai tampilan mandiri di `resources/views/errors/` (tanpa query database atau Vite) agar tetap dapat dirender saat database atau aset bermasalah. Tersedia halaman khusus 400, 401, 403, 404, 405, 419, 429, 500, dan 503, dengan fallback `4xx`/`5xx` untuk status lain. Halaman mengikuti palet hijau/amber situs, branding desa, tautan cepat, dan mode gelap dari `localStorage`. Saat `APP_DEBUG=true`, exception 500 tetap ditampilkan oleh renderer debug Laravel; halaman 500 kustom berlaku saat debug dinonaktifkan.
- Navigasi header dikelola lewat **Pengaturan → Menu Header** (`header_menu_items`): label, URL opsional, menu induk (maksimal dua level), tab baru, urutan, dan status aktif. `HeaderMenuSeeder` mempertahankan 16 item menu lama (8 menu utama, 8 submenu). Navbar desktop/mobile merender menu dari cache `public.header-menu` (10 menit); cache diinvalidasi oleh event `saved`/`deleted`. Tabel dikelompokkan per posisi, tombol Naik/Turun bekerja per saudara (top-level atau submenu), dan menghapus menu induk menghapus submenu lewat foreign key `cascadeOnDelete`.
- Halaman kustom dikelola lewat **Konten → Halaman** (`pages`): judul, slug unik, ringkasan, isi rich editor, sampul via Spatie Media Library, meta SEO, dan status terbit. URL publik `/halaman/{slug}` (`pages.show`) hanya melayani halaman terbit; draft 404. Route `/profil`, `/pemerintahan`, `/layanan`, dan `/kontak` tetap memakai data dinamis dari modul masing-masing. Halaman terbit ikut masuk `/sitemap.xml`, sedangkan gambar isi disimpan di `storage/app/public/pages/content`.
- Semua upload gambar raster diproses menjadi WebP yang dioptimalkan melalui `ImageOptimizer` (resize maksimum, kualitas 82, auto-orient, strip metadata) dan komponen Filament `WebpFileUpload`/`WebpMediaLibraryFileUpload`. Media library memakai conversion WebP: artikel (`thumb`/`card`), galeri (`thumb`/`large`), halaman (`webp`), slide (`webp`), serta logo/favicon/hero (`webp_icon`/`webp_favicon`/`webp_hero`); semua accessor URL memakai conversion tersebut. Gambar isi artikel/halaman juga disimpan sebagai WebP lewat `saveUploadedFileAttachmentUsing`. Command `images:convert-webp` memigrasikan path gambar lama, sedangkan `media-library:regenerate` membuat varian untuk media lama. Pengecualian yang disengaja: SVG/ICO dipertahankan (logo/favicon), `apple-touch-icon` memakai favicon asli karena iOS tidak mendukung WebP, dan URL gambar eksternal/placeholder (mis. picsum) tidak diunduh. File asli media library lama tetap disimpan sebagai master, tetapi URL yang disajikan selalu varian WebP.
- Artikel dan halaman memiliki koleksi media `attachments` untuk menyisipkan file pendukung (PDF, Word, Excel, PowerPoint, TXT, CSV; maksimal 10 MB per file, nama file dipertahankan). Form admin menyediakan field **Lampiran file**; file tampil sebagai daftar **Lampiran** yang dapat diunduh di bawah konten artikel/halaman melalui partial `partials/attachments.blade.php`.
- Widget **Sinergi Program** mempertahankan card, padding, heading, dan tautan “Semua tautan” seperti desain awal, tetapi grid kini satu kolom penuh dan setiap sel hanya berisi gambar dengan rasio asli (`h-auto w-full`, `rounded-xl`), serta seluruh gambar menjadi tautan. `ProgramLink` memakai koleksi media `image` (WebP, maksimal 1600 px); form admin hanya berisi gambar, nama internal/alt, tautan, dan urutan tanpa istilah “Program Desa”, dengan image editor bebas crop/putar/zoom tanpa rasio paksa. Record lama tanpa gambar otomatis tidak ditampilkan sampai gambar diunggah, dan cache `home.village-content` ikut dibersihkan saat media program dibuat/dihapus.
- Verifikasi manual (tanpa test otomatis): seluruh halaman publik (`/`, berita, kategori, galeri foto/album/video, profil, pemerintahan, layanan, kontak) merespons 200 tanpa `LazyLoadingViolationException`; menu header dari database tampil di desktop dan mobile pada semua halaman tersebut; Resource Menu Header dan Halaman (index dan edit) merespons 200 dengan sesi admin sementara; halaman uji terbit merespons 200 dengan judul/konten/meta yang benar, draft 404, dan URL-nya muncul di sitemap; URL WebP (logo, favicon, hero, thumbnail/full galeri, foto perangkat, gambar program) merespons 200 `image/webp`; komponen upload WebP diuji langsung dengan gambar sementara (menghasilkan `.webp`, resize benar) lalu dibersihkan; foto perangkat lama 1,2 MB berhasil dikonversi ke WebP sekitar 149 KB; gambar Sinergi Program diuji lewat record sementara (grid satu kolom penuh, tinggi tetap, hanya `img` + tautan, tanpa teks) lalu dibersihkan; warna latar palet diuji berubah pada Sinergi Program dan foto perangkat (termasuk tabel admin) lalu dikembalikan; slider Perangkat Desa dua kolom dan dialog pratinjau foto diuji lewat markup/HTTP; Pilar Desa diuji dengan data dan foto sementara di beranda, `/pemerintahan#pilar-desa`, serta Resource admin (index/create/edit) lalu dibersihkan; Resource Pengguna (index/create/edit) merespons 200 dengan form nama/email/password/konfirmasi/role; dashboard admin merespons 200 dan menampilkan statistik, artikel terbaru, agenda mendatang, serta komentar pending; lampiran halaman diuji dengan file TXT sementara (tampil di daftar Lampiran dan URL unduhan merespons 200) lalu dibersihkan; form admin Artikel/Halaman menampilkan field Lampiran file; `/sitemap.xml` dan `/robots.txt` merespons 200 dengan content-type yang benar; `/test` menjadi 404; `/admin/login` 200 dan `/admin/agendas` 302 ke login; `pnpm run build` dan Pint bersih.
- `preventLazyLoading` sengaja tidak diaktifkan untuk area admin agar panel Filament tidak terhenti oleh relasi yang dimuat lazy; audit CRUD/admin tetap perlu login manual.
- Panel admin memakai middleware `PreventAdminIndexing` yang mengirim `X-Robots-Tag: noindex, nofollow, noarchive` pada seluruh response panel. Path panel tidak dicantumkan di `robots.txt` maupun sitemap, dan tautan admin di halaman error 401 dihapus agar path rahasia tidak bocor dari halaman publik.

**Selesai bila:** tidak ada data hardcoded tersisa untuk konten desa dan halaman utama lolos pengecekan manual.

## 6. Urutan PR yang Disarankan

1. `chore/foundation` — admin access, aset logo, Tailwind Vite, seeder admin.
2. `feat/articles` — skema + Resource + halaman publik berita + hapus rute statis lama.
3. `feat/gallery` — album/foto/video + route album.
4. `feat/village-profile` — perangkat, statistik, program, layanan.
5. `feat/budget` — APBDes + chart dinamis.
6. `feat/widgets` — cuaca, pengunjung, arsip, agenda.
7. `chore/cleanup` — SEO, aksesibilitas, hapus kode mati.

## 7. Verifikasi (tanpa test)

| Langkah | Perintah / cara |
|---|---|
| Migrasi | `php artisan migrate` (+ `--seed` bila perlu) |
| Build aset | `pnpm run build` |
| Format PHP | `vendor/bin/pint <path> --format agent` |
| Rute | `php artisan route:list --except-vendor` |
| Halaman publik | Jalankan `php artisan serve`, cek status HTTP dan isi HTML (tanpa referensi CDN) |
| Panel admin | Login manual di `/admin` dengan akun admin, cek resource terkait |
| Konversi gambar | `php artisan images:convert-webp` lalu `php artisan media-library:regenerate` |
| Performa query | `DB::listen` / `preventLazyLoading()` saat cek manual |

```bash
php artisan migrate
php artisan images:convert-webp
php artisan media-library:regenerate
pnpm run build
vendor/bin/pint <path> --format agent
php artisan route:list --except-vendor
```

## 8. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Migrasi Tailwind CDN → Vite mengubah tampilan | Kerjakan di PR terpisah, bandingkan halaman sebelum/sesudah, pindahkan CSS kustom apa adanya |
| Data dummy (tahun 2026) tercampur data nyata | Seeder dipisah (`DemoSeeder` vs `ProductionSeeder`), tandai jelas |
| Upload gambar membengkak di storage | Batasi ukuran/tipe, generate thumbnail, gunakan disk `public` |
| Perubahan skema berulang | Finalisasi ERD di Fase 1 sebelum Resource ditulis |
| Filament 5 API berbeda dari versi lama | Cek `search-docs` (Boost) sebelum menulis Resource/Policy |

## 9. Keputusan Terbuka

### Sudah disepakati

1. **Akses admin**: kolom `is_admin` boolean + `canAccessPanel()`; belum ada peran lain.
2. **Akun admin awal**: di-seed dari `ADMIN_EMAIL`/`ADMIN_PASSWORD` di `.env`.
3. **Logo hero**: slot logo disembunyikan sampai aset final tersedia.
4. **Peran pengguna**: admin tunggal. Skema disiapkan agar role bisa ditambah tanpa migrasi ulang.
5. **Komentar artikel**: persisten + moderasi (default `pending`, tampil setelah `approved`).
6. **Database**: PostgreSQL untuk dev dan produksi.
7. **Aset frontend**: seluruh library dibundel Vite + pnpm (Chart.js, Swiper, Font Awesome, font Inter); tidak ada CDN di layout.
8. **Konten**: tetap data dummy; data resmi menyusul.
9. **URL artikel lama**: rute statis `/berita/contoh-artikel` dihapus (URL lama 404); semua tautan internal memakai route artikel DB.
10. **Form kontak**: tidak dibuat; footer memakai tautan `mailto:` dan `wa.me`.
11. **Media library & rich editor**: `spatie/laravel-medialibrary` 11 + `filament/spatie-laravel-media-library-plugin` 5 dipakai untuk gambar sampul. Tabel `media` tetap ada; kolom `body` nullable berasal dari implementasi provider lama dan dipertahankan. Gambar isi artikel kini dikelola Advanced Rich Editor 1.9.0 pada disk publik `articles/content` (bukan koleksi Spatie `body`); crop inline, alt text, caption, dan resize gambar tersedia. Tinggi editor memakai CSS resize vertikal dengan batas min/max.

### Masih perlu jawaban

- **Kredensial PostgreSQL dev**: apakah server lokal sudah siap dan boleh saya set `.env` sekarang, atau Anda yang menyiapkan?
- **Kata sandi admin awal**: set sendiri di `.env` atau saya generate dan tampilkan sekali?

### 9.1 Catatan: form kontak (disepakati A)

Keputusan: **A — tanpa form**. `#kontak` tetap footer, tetapi tautan email/WhatsApp/alamat dibuat fungsional (`mailto:`, `wa.me`, Google Maps) dan nilainya disimpan di konfigurasi/tabel settings agar bisa diubah tanpa deploy. Tabel `contact_messages`, notifikasi email, dan proteksi spam tidak dibuat.

Rincian opsi yang dipertimbangkan:

| Opsi | Isi pekerjaan | Kelebihan | Kekurangan |
|---|---|---|---|
| **A. Tanpa form (dipilih)** | Footer menampilkan email/WhatsApp/alamat yang bisa diklik | Nol biaya, nol spam | Tidak ada rekap pesan |
| B. Form → simpan DB | Tabel `contact_messages` + Resource Filament + email + anti-spam | Pesan terpusat | Perlu maintenance & proteksi spam |
| C. Form → email langsung | Tanpa tabel, langsung SMTP | Tidak perlu panel | Pesan mudah hilang, rawan spam |

### 9.2 Catatan: URL artikel lama (disepakati B)

Saat ini artikel contoh dilayani rute statis `Route::view('/berita/contoh-artikel', 'pages.article')`. Setelah artikel pindah ke database, alamat artikel mengikuti slug, mis. `/berita/peresmian-jalan-desa-pakel-tahap-ii`.

Keputusan: **B — rute lama dihapus**, URL `/berita/contoh-artikel` menjadi 404. Konsekuensi yang harus ditangani:

- Semua tautan internal (beranda, breadcrumb, share) diperbarui ke `route('articles.show', $article)`.
- Tidak ada redirect; bila nanti ada link eksternal yang perlu diselamatkan, redirect bisa ditambahkan sebagai PR kecil terpisah.
- Test `ArticleTest` memastikan `/berita/contoh-artikel` mengembalikan 404 (dicek manual selama kebijakan tanpa-test berlaku).

## 10. Definition of Done (proyek)

- [x] Seluruh konten beranda, berita, agenda, galeri, profil, dan APBDes berasal dari database.
- [ ] Semua entitas dapat dikelola dari `/admin` oleh admin tanpa menyentuh kode.
- [x] `pnpm run build` sukses dan halaman publik tampil normal.
- [x] Pint bersih, tidak ada `const` konten hardcoded yang tersisa.
- [ ] Akses `/admin` terlindungi (dicek manual dengan akun admin dan non-admin).
