@include('errors.page', [
    'status' => 401,
    'eyebrow' => 'Autentikasi diperlukan',
    'title' => 'Anda perlu masuk terlebih dahulu',
    'description' => 'Halaman ini hanya dapat diakses setelah autentikasi. Silakan masuk melalui panel admin atau kembali ke halaman publik.',
    'primaryLabel' => 'Masuk ke panel admin',
    'primaryUrl' => url('/admin/login'),
    'secondaryLabel' => 'Kembali ke beranda',
    'secondaryUrl' => url('/'),
])
