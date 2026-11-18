@include('errors.page', [
    'status' => 403,
    'eyebrow' => 'Akses ditolak',
    'title' => 'Anda tidak memiliki akses ke halaman ini',
    'description' => 'Halaman atau tindakan ini dibatasi untuk pengguna tertentu. Jika menurut Anda ini sebuah kekeliruan, silakan hubungi pengelola website desa.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
