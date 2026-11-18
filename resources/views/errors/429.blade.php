@include('errors.page', [
    'status' => 429,
    'eyebrow' => 'Terlalu banyak permintaan',
    'title' => 'Permintaan Anda terlalu sering',
    'description' => 'Sistem membatasi jumlah permintaan dalam waktu singkat untuk menjaga layanan tetap stabil. Silakan tunggu sebentar, lalu coba lagi.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
