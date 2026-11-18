@include('errors.page', [
    'status' => 405,
    'eyebrow' => 'Metode tidak diizinkan',
    'title' => 'Permintaan ini tidak didukung',
    'description' => 'Alamat halaman benar, tetapi metode yang digunakan tidak diizinkan. Silakan kembali ke beranda dan akses halaman melalui tautan yang tersedia.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Lihat berita',
    'secondaryUrl' => url('/berita'),
])
