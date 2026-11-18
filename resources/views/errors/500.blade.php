@include('errors.page', [
    'status' => 500,
    'eyebrow' => 'Kesalahan server',
    'title' => 'Terjadi kesalahan saat memproses permintaan',
    'description' => 'Kami tidak dapat menyelesaikan permintaan ini untuk sementara. Silakan coba beberapa saat lagi atau kembali ke beranda. Jika masalah berlanjut, hubungi pengelola website desa.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
