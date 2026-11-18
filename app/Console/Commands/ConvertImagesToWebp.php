<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\HomeSlide;
use App\Models\Page;
use App\Models\Photo;
use App\Models\Video;
use App\Models\VillageOfficial;
use App\Services\ImageOptimizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('images:convert-webp {--dry-run : Tampilkan gambar yang akan dikonversi tanpa menulis perubahan}')]
#[Description('Konversi gambar lokal lama menjadi WebP yang dioptimalkan')]
class ConvertImagesToWebp extends Command
{
    /**
     * @var array<string, string>
     */
    private array $convertedPaths = [];

    public function handle(ImageOptimizer $optimizer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $converted = 0;

        $fields = [
            [VillageOfficial::class, 'photo', 1200],
            [Video::class, 'thumbnail', 1600],
            [Article::class, 'cover_image', 1600],
            [HomeSlide::class, 'image', 1600],
            [Photo::class, 'path', 2000],
        ];

        foreach ($fields as [$modelClass, $field, $maxWidth]) {
            $modelClass::query()
                ->whereNotNull($field)
                ->where($field, '!=', '')
                ->cursor()
                ->each(function (Model $model) use ($field, $maxWidth, $dryRun, $optimizer, &$converted): void {
                    $path = (string) $model->getAttribute($field);

                    if ($this->isExternalPath($path)) {
                        return;
                    }

                    $newPath = $this->convertPath($path, $maxWidth, $dryRun, $optimizer);

                    if ($newPath === null) {
                        return;
                    }

                    if (! $dryRun && $newPath !== $path) {
                        $model->update([$field => $newPath]);
                    }

                    $converted++;
                    $this->line(sprintf('%s: %s -> %s', class_basename($model), $path, $newPath));
                });
        }

        foreach ([[Article::class, 'body'], [Page::class, 'content']] as [$modelClass, $field]) {
            $modelClass::query()
                ->whereNotNull($field)
                ->where($field, '!=', '')
                ->cursor()
                ->each(function (Model $model) use ($field, $dryRun, $optimizer, &$converted): void {
                    $html = (string) $model->getAttribute($field);

                    if (! str_contains($html, '<img')) {
                        return;
                    }

                    $localConversions = [];
                    $updatedHtml = preg_replace_callback(
                        '/<img\b[^>]*\bsrc=(["\'])(.*?)\1[^>]*>/i',
                        function (array $matches) use ($dryRun, $optimizer, &$converted, &$localConversions): string {
                            $src = html_entity_decode($matches[2], ENT_QUOTES);
                            $newSrc = $localConversions[$src] ?? $this->convertContentImage($src, $dryRun, $optimizer);

                            if ($newSrc === null || $newSrc === $src) {
                                return $matches[0];
                            }

                            $localConversions[$src] = $newSrc;
                            $converted++;

                            return str_replace($matches[2], htmlspecialchars($newSrc, ENT_QUOTES), $matches[0]);
                        },
                        $html,
                    );

                    if (! $dryRun && is_string($updatedHtml) && $updatedHtml !== $html) {
                        $model->update([$field => $updatedHtml]);
                    }
                });
        }

        $this->info(sprintf(
            '%s %d gambar. Jalankan media-library:regenerate untuk konversi media library.',
            $dryRun ? 'Akan dikonversi:' : 'Selesai dikonversi:',
            $converted,
        ));

        return self::SUCCESS;
    }

    /**
     * Convert one stored public image and remember the mapping for the current run.
     */
    private function convertPath(string $path, int $maxWidth, bool $dryRun, ImageOptimizer $optimizer): ?string
    {
        if (Str::endsWith(Str::lower($path), '.webp')) {
            return null;
        }

        if (isset($this->convertedPaths[$path])) {
            return $this->convertedPaths[$path];
        }

        $storage = Storage::disk('public');

        if (! $storage->exists($path)) {
            return null;
        }

        try {
            $localPath = $storage->path($path);
        } catch (\Throwable) {
            return null;
        }

        if (! $optimizer->isRasterImage($localPath)) {
            return null;
        }

        if ($dryRun) {
            return $path === '' ? null : $path;
        }

        $newPath = $optimizer->replaceStoredFile('public', $path, $maxWidth);

        if ($newPath !== null) {
            $this->convertedPaths[$path] = $newPath;
        }

        return $newPath;
    }

    /**
     * Convert a local image referenced by rich content and return the replacement URL.
     */
    private function convertContentImage(string $src, bool $dryRun, ImageOptimizer $optimizer): ?string
    {
        $urlPath = parse_url($src, PHP_URL_PATH);

        if (! is_string($urlPath)) {
            return null;
        }

        $storagePath = match (true) {
            str_starts_with($urlPath, '/storage/') => Str::after($urlPath, '/storage/'),
            str_starts_with($urlPath, 'storage/') => Str::after($urlPath, 'storage/'),
            default => null,
        };

        if (! is_string($storagePath) || $storagePath === '') {
            return null;
        }

        $newPath = $this->convertPath($storagePath, 1920, $dryRun, $optimizer);

        if ($newPath === null) {
            return null;
        }

        return str_replace('/storage/'.$storagePath, '/storage/'.$newPath, $src);
    }

    /**
     * Determine whether a stored path points away from the public disk.
     */
    private function isExternalPath(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://', '//']);
    }
}
