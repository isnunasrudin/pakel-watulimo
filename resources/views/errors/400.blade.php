@include('errors.page', [
    'status' => 400,
    'eyebrow' => 'Permintaan tidak valid',
    'title' => 'Permintaan tidak dapat diproses',
    'description' => 'Data yang dikirim tidak lengkap atau formatnya tidak dikenali. Silakan kembali dan coba lagi dari halaman sebelumnya.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
