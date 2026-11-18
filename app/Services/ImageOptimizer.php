<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Image\Enums\Constraint;
use Spatie\Image\Image;

class ImageOptimizer
{
    public const DEFAULT_MAX_WIDTH = 1920;

    public const DEFAULT_QUALITY = 82;

    /**
     * Store an uploaded raster image as an optimized WebP file.
     */
    public function storeUploadedFile(
        UploadedFile $file,
        string $disk,
        ?string $directory = null,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY,
    ): string {
        $path = $this->pathForUploadedFile($file, $directory);
        $storage = Storage::disk($disk);

        if (! $storage->put($path, $this->webpContents($file, $maxWidth, $quality))) {
            throw new RuntimeException('Gambar WebP gagal disimpan.');
        }

        rescue(fn () => $storage->setVisibility($path, 'public'), report: false);

        return $path;
    }

    /**
     * Convert an uploaded image to WebP and return its binary contents.
     */
    public function webpContents(
        UploadedFile $file,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY,
    ): string {
        $sourcePath = $file->getRealPath();

        if ($sourcePath === false) {
            throw new RuntimeException('Berkas gambar sementara tidak ditemukan.');
        }

        return $this->webpContentsFromPath($sourcePath, $maxWidth, $quality);
    }

    /**
     * Convert an existing local file to WebP in place, returning the new path.
     */
    public function replaceStoredFile(
        string $disk,
        string $path,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY,
    ): ?string {
        $storage = Storage::disk($disk);

        if (! $storage->exists($path)) {
            return null;
        }

        try {
            $sourcePath = $storage->path($path);
        } catch (\Throwable) {
            return null;
        }

        if (! $this->isRasterImage($sourcePath)) {
            return null;
        }

        $contents = $this->webpContentsFromPath($sourcePath, $maxWidth, $quality);
        $newPath = $this->webpPathFor($disk, $path);
        $storage->put($newPath, $contents);
        rescue(fn () => $storage->setVisibility($newPath, 'public'), report: false);

        if ($newPath !== $path) {
            $storage->delete($path);
        }

        return $newPath;
    }

    /**
     * Determine whether the file is a raster image supported by WebP conversion.
     */
    public function isRasterImage(string $path): bool
    {
        if (! is_file($path)) {
            return false;
        }

        $mimeType = mime_content_type($path);

        return is_string($mimeType)
            && in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true);
    }

    /**
     * Convert a local raster image to WebP binary.
     */
    public function webpContentsFromPath(
        string $sourcePath,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY,
    ): string {
        $size = @getimagesize($sourcePath);

        if ($size === false) {
            throw new RuntimeException('Format gambar tidak dikenali.');
        }

        $targetPath = tempnam(sys_get_temp_dir(), 'pakel-webp-');

        if ($targetPath === false) {
            throw new RuntimeException('Berkas sementara WebP gagal dibuat.');
        }

        try {
            $image = Image::load($sourcePath);

            if (($size[0] ?? 0) > $maxWidth) {
                $image->width($maxWidth, [Constraint::PreserveAspectRatio, Constraint::DoNotUpsize]);
            }

            $image->format('webp')->quality($quality)->save($targetPath);
            $contents = file_get_contents($targetPath);

            if ($contents === false || $contents === '') {
                throw new RuntimeException('Konversi WebP menghasilkan berkas kosong.');
            }

            return $contents;
        } finally {
            if (is_file($targetPath)) {
                unlink($targetPath);
            }
        }
    }

    /**
     * Build a unique public path for an uploaded image.
     */
    private function pathForUploadedFile(UploadedFile $file, ?string $directory): string
    {
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'gambar';
        $fileName = $baseName.'-'.Str::lower(Str::random(10)).'.webp';
        $directory = trim((string) $directory, '/');

        return $directory === '' ? $fileName : $directory.'/'.$fileName;
    }

    /**
     * Build a WebP path that mirrors the original file location.
     */
    private function webpPathFor(string $disk, string $path): string
    {
        $directory = dirname($path);
        $baseName = pathinfo($path, PATHINFO_FILENAME).'.webp';
        $candidate = $directory === '.' ? $baseName : $directory.'/'.$baseName;

        if ($candidate === $path) {
            return $candidate;
        }

        $counter = 1;

        while (Storage::disk($disk)->exists($candidate)) {
            $candidate = ($directory === '.' ? '' : $directory.'/')
                .pathinfo($path, PATHINFO_FILENAME).'-'.$counter.'.webp';
            $counter++;
        }

        return $candidate;
    }
}
