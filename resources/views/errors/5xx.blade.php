@php
    $status = isset($exception) && $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
        ? $exception->getStatusCode()
        : 500;
@endphp
@include('errors.page', [
    'status' => $status,
    'eyebrow' => 'Kesalahan server',
    'title' => 'Layanan sedang mengalami kendala',
    'description' => 'Kami tidak dapat menyelesaikan permintaan ini untuk sementara. Silakan coba beberapa saat lagi atau kembali ke beranda. Jika masalah berlanjut, hubungi pengelola website desa.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
