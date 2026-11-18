@include('errors.page', [
    'status' => 404,
    'eyebrow' => 'Halaman tidak ditemukan',
    'title' => 'Halaman yang Anda cari tidak tersedia',
    'description' => 'Alamat mungkin sudah berubah, salah ketik, atau halaman telah dihapus. Silakan kembali ke beranda atau telusuri berita dan galeri desa.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Lihat berita',
    'secondaryUrl' => url('/berita'),
])
