@include('errors.page', [
    'status' => 401,
    'eyebrow' => 'Autentikasi diperlukan',
    'title' => 'Anda perlu masuk terlebih dahulu',
    'description' => 'Halaman ini hanya dapat diakses setelah autentikasi. Silakan masuk melalui panel admin, lalu buka kembali halaman yang dituju.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
