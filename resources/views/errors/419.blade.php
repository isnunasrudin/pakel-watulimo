@include('errors.page', [
    'status' => 419,
    'eyebrow' => 'Sesi berakhir',
    'title' => 'Sesi Anda telah kedaluwarsa',
    'description' => 'Halaman terlalu lama dibiarkan terbuka atau sesi keamanan Anda sudah berakhir. Silakan kembali ke halaman sebelumnya, muat ulang, lalu kirimkan formulir sekali lagi.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
