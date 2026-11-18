<?php

namespace Tests\Feature;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Memetakan mekanisme penyaringan MIME pada upload gambar.
 *
 * Sengaja TIDAK memuat payload yang bisa dieksekusi: isinya hanya penanda
 * teks inert, sehingga test ini aman dijalankan di environment mana pun.
 *
 * Di aplikasi ini `FileUpload::make(...)->image()` menghasilkan rule
 * `mimetypes:image/*`, yang menilai isi lewat finfo dan sama sekali tidak
 * melihat nama file. Test di bawah menunjukkan konsekuensi tiap lapisan.
 */
class UploadMimeValidationTest extends TestCase
{
    private const MARKER = 'PROBE-MARKER-NOT-EXECUTABLE';

    /**
     * @var list<string>
     */
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->temporaryFiles = [];

        parent::tearDown();
    }

    public function test_client_supplied_mime_type_is_not_trusted(): void
    {
        $file = $this->probe('probe.png', "teks biasa, bukan gambar\n", 'image/png');

        $this->assertSame('image/png', $file->getClientMimeType());
        $this->assertSame('text/plain', $file->getMimeType());

        $this->assertFalse(
            $this->passes('mimetypes:image/*', $file),
            'Mengubah Content-Type di request tidak menolong: validasi membaca isi file.',
        );
    }

    public function test_magic_bytes_alone_pass_mimetype_validation(): void
    {
        $file = $this->probe('probe.gif', 'GIF89a'."\n".self::MARKER."\n");

        $this->assertSame('image/gif', $file->getMimeType());

        $this->assertTrue(
            $this->passes('mimetypes:image/*', $file),
            'Enam byte header pertama sudah cukup untuk lolos `mimetypes:image/*`.',
        );

        $this->assertNotFalse(
            getimagesize($file->getRealPath()),
            'getimagesize() pun hanya membaca header, jadi tidak menutup celah ini.',
        );

        $this->assertStringContainsString(self::MARKER, (string) file_get_contents($file->getRealPath()));
    }

    public function test_known_php_extensions_are_rejected(): void
    {
        foreach (['php', 'phtml', 'phar', 'PHP'] as $extension) {
            $file = $this->probe("probe.{$extension}", $this->validGif());

            $this->assertSame('image/gif', $file->getMimeType());
            $this->assertFalse(
                $this->passes('mimetypes:image/*', $file),
                "Ekstensi .{$extension} ditolak karena masuk blocklist PHP di validator.",
            );
        }
    }

    public function test_extensions_outside_the_blocklist_still_pass(): void
    {
        foreach (['pht', 'phps', 'inc'] as $extension) {
            $file = $this->probe("probe.{$extension}", $this->validGif());

            $this->assertTrue(
                $this->passes('mimetypes:image/*', $file),
                "Ekstensi .{$extension} tidak ada di blocklist, jadi lolos validasi.",
            );
        }

        $doubleExtension = $this->probe('probe.php.gif', $this->validGif());

        $this->assertTrue(
            $this->passes('mimetypes:image/*', $doubleExtension),
            'Yang diperiksa hanya ekstensi terakhir, sehingga probe.php.gif lolos.',
        );
        $this->assertSame('gif', $doubleExtension->getClientOriginalExtension());
    }

    public function test_bytes_appended_to_a_real_image_survive_validation_until_reencoded(): void
    {
        $polyglot = $this->validPng()."\n".self::MARKER."\n";
        $file = $this->probe('probe.png', $polyglot);

        $this->assertTrue(
            $this->passes('mimetypes:image/*', $file),
            'Gambar sah tetap lolos walau ada data tambahan di belakangnya.',
        );

        $decoded = imagecreatefromstring($polyglot);
        $this->assertInstanceOf(GdImage::class, $decoded);

        ob_start();
        imagepng($decoded);
        $reencoded = (string) ob_get_clean();

        $this->assertStringContainsString(self::MARKER, $polyglot);
        $this->assertNotSame('', $reencoded);
        $this->assertStringNotContainsString(
            self::MARKER,
            $reencoded,
            'Re-encode membuang semua byte di luar struktur gambar.',
        );
    }

    public function test_svg_passes_image_validation_because_it_is_a_markup_document(): void
    {
        $svg = <<<'SVG'
            <?xml version="1.0" encoding="UTF-8"?>
            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8">
                <rect width="8" height="8" fill="#000"/>
                <title>probe</title>
            </svg>
            SVG;

        $file = $this->probe('probe.svg', $svg);

        $this->assertSame('image/svg+xml', $file->getMimeType());

        $this->assertTrue(
            $this->passes('mimetypes:image/*', $file),
            'SVG lolos sebagai gambar, padahal isinya dokumen yang boleh memuat script.',
        );
    }

    private function probe(string $name, string $content, ?string $clientMimeType = null): UploadedFile
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'upload-probe-');
        file_put_contents($path, $content);

        $this->temporaryFiles[] = $path;

        return new UploadedFile($path, $name, $clientMimeType, null, true);
    }

    private function validGif(): string
    {
        ob_start();
        imagegif(imagecreatetruecolor(2, 2));

        return (string) ob_get_clean();
    }

    private function validPng(): string
    {
        ob_start();
        imagepng(imagecreatetruecolor(2, 2));

        return (string) ob_get_clean();
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function passes(string $rule, UploadedFile $file): bool
    {
        return Validator::make(['file' => $file], ['file' => $rule])->passes();
    }
}
