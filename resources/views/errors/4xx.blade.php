@php
    $status = isset($exception) && $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
        ? $exception->getStatusCode()
        : 400;
@endphp
@include('errors.page', [
    'status' => $status,
    'eyebrow' => 'Permintaan tidak dapat diproses',
    'title' => 'Permintaan Anda tidak dapat dilanjutkan',
    'description' => 'Ada bagian dari permintaan yang tidak sesuai atau tidak lagi tersedia. Silakan kembali ke beranda dan akses halaman melalui tautan yang tersedia.',
    'primaryLabel' => 'Kembali ke beranda',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Hubungi kami',
    'secondaryUrl' => url('/kontak'),
])
