@include('errors.page', [
    'status' => 503,
    'eyebrow' => 'Layanan dalam pemeliharaan',
    'title' => 'Website sedang dalam pemeliharaan',
    'description' => 'Kami sedang melakukan pembaruan agar layanan berjalan lebih baik. Silakan kembali beberapa saat lagi.',
    'primaryLabel' => 'Coba lagi',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
