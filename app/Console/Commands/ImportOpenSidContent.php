<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use App\Services\ImageOptimizer;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

#[Signature('opensid:import {--source= : Direktori hasil export (default storage/app/private/opensid-import)} {--only= : Bagian yang diimpor: categories,articles,pages,agendas} {--dry-run : Validasi tanpa menulis} {--skip-media : Jangan salin/konversi media}')]
#[Description('Impor kategori, artikel, halaman, dan agenda OpenSID dari hasil export JSON')]
class ImportOpenSidContent extends Command
{
    private string $source;

    private bool $dryRun = false;

    private bool $skipMedia = false;

    private ImageOptimizer $optimizer;

    private int $defaultCategoryId = 0;

    /**
     * @var array<int, int>
     */
    private array $categoryIds = [];

    /**
     * @var array<string, mixed>
     */
    private array $report = [];

    public function handle(ImageOptimizer $optimizer): int
    {
        $this->optimizer = $optimizer;

        // Beberapa lampiran lama > 10 MB; naikkan hanya untuk proses impor.
        config(['media-library.max_file_size' => 25 * 1024 * 1024]);

        $this->source = rtrim(
            (string) ($this->option('source') ?: storage_path('app/private/opensid-import')),
            '/'
        );
        $this->dryRun = (bool) $this->option('dry-run');
        $this->skipMedia = (bool) $this->option('skip-media');

        if (! is_dir($this->source)) {
            $this->error("Direktori sumber tidak ditemukan: {$this->source}");

            return self::FAILURE;
        }

        $only = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($this->option('only') ?: 'categories,articles,pages,agendas'))
        )));

        $admin = User::query()->where('is_admin', true)->orderBy('id')->first();

        if (! $admin) {
            $this->error('Tidak ada user admin. Jalankan AdminUserSeeder terlebih dahulu.');

            return self::FAILURE;
        }

        $this->report = [
            'started_at' => now()->toIso8601String(),
            'source' => $this->source,
            'dry_run' => $this->dryRun,
            'skip_media' => $this->skipMedia,
            'only' => $only,
            'counts' => [
                'categories' => ['processed' => 0, 'created' => 0, 'updated' => 0],
                'articles' => ['processed' => 0, 'created' => 0, 'updated' => 0],
                'pages' => ['processed' => 0, 'created' => 0, 'updated' => 0],
                'agendas' => ['processed' => 0, 'created' => 0, 'updated' => 0],
            ],
            'media' => ['covers' => 0, 'attachments' => 0, 'gallery' => 0, 'inline' => 0],
            'errors' => [],
        ];

        $this->info(sprintf(
            '%s impor dari %s%s',
            $this->dryRun ? 'DRY-RUN' : 'Mulai',
            $this->source,
            $this->skipMedia ? ' (media dilewati)' : ''
        ));

        if (in_array('categories', $only, true) || in_array('articles', $only, true)) {
            $this->importCategories();
        }

        if (in_array('articles', $only, true)) {
            $this->importArticles($admin);
        }

        if (in_array('pages', $only, true)) {
            $this->importPages();
        }

        if (in_array('agendas', $only, true)) {
            $this->importAgendas();
        }

        $this->report['finished_at'] = now()->toIso8601String();
        $this->writeReport();
        $this->printSummary();

        return $this->report['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }

    private function importCategories(): void
    {
        $rows = $this->readJson('category_map.json');

        foreach ($rows as $row) {
            if ($row['legacy_id'] === null) {
                continue;
            }

            $legacyId = (int) $row['legacy_id'];

            if ($this->dryRun) {
                $this->categoryIds[$legacyId] = 0;
                $this->report['counts']['categories']['processed']++;

                continue;
            }

            $category = Category::query()->updateOrCreate(
                ['slug' => $row['target_slug']],
                [
                    'name' => $row['target_name'],
                    'description' => null,
                    'color' => $row['color'],
                    'order' => (int) $row['order'],
                ]
            );

            $this->categoryIds[$legacyId] = $category->id;
            $this->report['counts']['categories']['processed']++;
            $this->report['counts']['categories'][$category->wasRecentlyCreated ? 'created' : 'updated']++;
        }

        if (! $this->dryRun) {
            $this->defaultCategoryId = (int) Category::query()
                ->where('slug', 'berita-desa')
                ->value('id');
        }
    }

    private function importArticles(User $admin): void
    {
        $rows = $this->readJson('articles.json')['rows'] ?? [];

        foreach ($rows as $row) {
            $label = 'article:'.$row['legacy_id'];

            try {
                $body = $this->prepareBody($row, 'articles');

                if ($this->dryRun) {
                    $this->validateMedia($row);
                    $this->report['counts']['articles']['processed']++;

                    continue;
                }

                $publishedAt = $this->parseDate($row['published_at'] ?? null);

                $article = Article::query()->updateOrCreate(
                    ['legacy_id' => (int) $row['legacy_id']],
                    [
                        'user_id' => $admin->id,
                        'category_id' => $this->categoryIdFor($row['category_legacy_id'] ?? null),
                        'title' => $row['title'],
                        'slug' => $row['slug'],
                        'excerpt' => $row['excerpt'] ?: null,
                        'body' => $body,
                        'is_pinned' => (bool) $row['is_pinned'],
                        'is_featured' => (bool) $row['is_featured'],
                        'status' => $row['status'],
                        'published_at' => $publishedAt,
                        'views_count' => (int) $row['views_count'],
                        'meta_title' => $row['meta_title'] ?: null,
                        'meta_description' => $row['meta_description'] ?: null,
                    ]
                );

                $this->syncTimestamps('articles', $article->id, $publishedAt);
                $this->attachCover($article, $row['cover'] ?? null);
                $this->attachAttachment($article, $row['attachment'] ?? null);

                $this->report['counts']['articles']['processed']++;
                $this->report['counts']['articles'][$article->wasRecentlyCreated ? 'created' : 'updated']++;
            } catch (Throwable $exception) {
                $this->recordError($label, $exception);
            }
        }
    }

    private function importPages(): void
    {
        $this->copyPageAsset();

        $rows = $this->readJson('pages.json')['rows'] ?? [];

        foreach ($rows as $row) {
            $label = 'page:'.$row['legacy_id'];

            try {
                $body = $this->prepareBody($row, 'pages');

                if ($this->dryRun) {
                    $this->validateMedia($row);
                    $this->report['counts']['pages']['processed']++;

                    continue;
                }

                $publishedAt = $this->parseDate($row['published_at'] ?? null);

                $page = Page::query()->updateOrCreate(
                    ['legacy_id' => (int) $row['legacy_id']],
                    [
                        'title' => $row['title'],
                        'slug' => $row['slug'],
                        'excerpt' => $row['excerpt'] ?: null,
                        'content' => $body,
                        'meta_title' => $row['meta_title'] ?: null,
                        'meta_description' => $row['meta_description'] ?: null,
                        'is_published' => (bool) $row['is_published'],
                    ]
                );

                $this->syncTimestamps('pages', $page->id, $publishedAt);
                $this->attachCover($page, $row['cover'] ?? null);
                $this->attachAttachment($page, $row['attachment'] ?? null);

                $this->report['counts']['pages']['processed']++;
                $this->report['counts']['pages'][$page->wasRecentlyCreated ? 'created' : 'updated']++;
            } catch (Throwable $exception) {
                $this->recordError($label, $exception);
            }
        }
    }

    private function importAgendas(): void
    {
        $rows = $this->readJson('agendas.json')['rows'] ?? [];

        foreach ($rows as $row) {
            $label = 'agenda:'.$row['legacy_id'];

            try {
                $body = $this->cleanHtml($row['body_html'] ?? '');

                if ($this->dryRun) {
                    $this->report['counts']['agendas']['processed']++;

                    continue;
                }

                $startsAt = $this->parseDate($row['starts_at'] ?? $row['published_at'] ?? null) ?? now();

                $agenda = Agenda::query()->updateOrCreate(
                    ['legacy_id' => (int) $row['legacy_id']],
                    [
                        'title' => $row['title'],
                        'slug' => $row['slug'],
                        'description' => $body ?: null,
                        'location' => $row['location'] ?? null,
                        'starts_at' => $startsAt,
                        'ends_at' => null,
                        'is_published' => (bool) $row['is_published'],
                    ]
                );

                $this->syncTimestamps('agendas', $agenda->id, $startsAt);

                $this->report['counts']['agendas']['processed']++;
                $this->report['counts']['agendas'][$agenda->wasRecentlyCreated ? 'created' : 'updated']++;
            } catch (Throwable $exception) {
                $this->recordError($label, $exception);
            }
        }
    }

    /**
     * Bersihkan HTML dan sisipkan gambar inline + galeri.
     *
     * @param  array<string, mixed>  $row
     */
    private function prepareBody(array $row, string $type): string
    {
        $body = $this->cleanHtml($row['body_html'] ?? '');

        if ($this->skipMedia) {
            return $body;
        }

        $inlineDirectory = $type.'/content/'.$row['legacy_id'];
        $galleryDirectory = $type.'/gallery/'.$row['legacy_id'];

        if (! $this->dryRun) {
            Storage::disk('public')->deleteDirectory($inlineDirectory);
            Storage::disk('public')->deleteDirectory($galleryDirectory);
        }

        foreach (($row['inline_images'] ?? []) as $index => $image) {
            if (! empty($image['external']) || empty($image['source_file'])) {
                continue;
            }

            $target = sprintf('%s/%02d.webp', $inlineDirectory, $index + 1);
            $this->storeWebp($this->stagingPath($image['source_file']), $target, 1920);

            $body = str_replace($image['url'], '/storage/'.$target, $body);
            $this->report['media']['inline']++;
        }

        $figures = [];

        foreach (($row['gallery'] ?? []) as $index => $image) {
            if (empty($image['source_file'])) {
                continue;
            }

            $target = sprintf('%s/%02d.webp', $galleryDirectory, $index + 1);
            $this->storeWebp($this->stagingPath($image['source_file']), $target, 1600);

            $alt = htmlspecialchars($row['title'].' — foto '.($index + 1), ENT_QUOTES, 'UTF-8');
            $figures[] = '<figure style="margin:0"><img src="/storage/'.$target.'" alt="'.$alt.'" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></figure>';
            $this->report['media']['gallery']++;
        }

        if ($figures !== []) {
            $body .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.75rem;margin:1.5rem 0">'
                .implode('', $figures)
                .'</div>';
        }

        return $body;
    }

    private function cleanHtml(string $html): string
    {
        // Kumpulkan ID video dari iframe yang sudah valid, agar gambar YouTube
        // duplikat tidak menghasilkan iframe kedua.
        $existingEmbeds = [];

        if (preg_match_all('/<iframe\b[^>]*\bsrc=["\']([^"\']*youtube\.com\/embed\/[^"\']*)["\']/i', $html, $iframeMatches)) {
            foreach ($iframeMatches[1] as $iframeSrc) {
                if (preg_match('#youtube\.com/embed/([A-Za-z0-9_-]+)#i', html_entity_decode($iframeSrc, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $embedId)) {
                    $existingEmbeds[$embedId[1]] = true;
                }
            }
        }

        // Perbaiki iframe YouTube yang ter-escape di dalam atribut src <img>.
        $html = preg_replace_callback(
            '/<img\b[^>]*\bsrc=(["\'])(.*?)\1[^>]*>/is',
            static function (array $matches) use ($existingEmbeds): string {
                $src = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if (preg_match('#youtube\.com/embed/([A-Za-z0-9_-]+)#i', $src, $embed)) {
                    if (isset($existingEmbeds[$embed[1]])) {
                        return '';
                    }

                    return '<iframe src="https://www.youtube.com/embed/'.$embed[1].'" width="100%" height="400" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
                }

                if (preg_match('#youtube\.com/shorts/([A-Za-z0-9_-]+)#i', $src, $short)) {
                    if (isset($existingEmbeds[$short[1]])) {
                        return '';
                    }

                    return '<iframe src="https://www.youtube.com/embed/'.$short[1].'" width="100%" height="400" frameborder="0" allowfullscreen></iframe>';
                }

                return $matches[0];
            },
            $html
        ) ?? $html;

        // Hapus atribut sisa ekspor Google Docs.
        $html = preg_replace('/\sdata-(?:path-to-node|index-in-node)\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html) ?? $html;

        // Hapus script dan event handler.
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? $html;
        $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/javascript\s*:/i', '', $html) ?? $html;

        // Aset halaman lama.
        $html = str_replace(
            [
                'https://pakel-watulimo.desa.id/desa/dana_desa.html',
                'http://pakel-watulimo.desa.id/desa/dana_desa.html',
            ],
            '/storage/pages/dana_desa.html',
            $html
        );

        return trim($html);
    }

    private function attachCover(Article|Page $model, ?array $cover): void
    {
        if ($this->skipMedia) {
            return;
        }

        if (! $this->dryRun) {
            $model->clearMediaCollection('cover');
        }

        if (! $cover || empty($cover['source_file'])) {
            return;
        }

        $path = $this->stagingPath($cover['source_file']);
        $this->assertStagingFile($path);

        $this->report['media']['covers']++;

        if ($this->dryRun) {
            return;
        }

        $model->addMedia($path)
            ->usingFileName($this->mediaFileName($cover['source_file']))
            ->preservingOriginal()
            ->toMediaCollection('cover', 'public');
    }

    private function attachAttachment(Article|Page $model, ?array $attachment): void
    {
        if ($this->skipMedia || ! $attachment || empty($attachment['source_file'])) {
            return;
        }

        $path = $this->stagingPath($attachment['source_file']);
        $this->assertStagingFile($path);

        $this->report['media']['attachments']++;

        if ($this->dryRun) {
            return;
        }

        $model->clearMediaCollection('attachments');

        $model->addMedia($path)
            ->usingFileName(basename($attachment['source_file']))
            ->preservingOriginal()
            ->toMediaCollection('attachments', 'public');
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function validateMedia(array $row): void
    {
        foreach (['cover', 'attachment'] as $key) {
            $item = $row[$key] ?? null;

            if ($item && ! empty($item['source_file'])) {
                $this->assertStagingFile($this->stagingPath($item['source_file']));
            }
        }

        foreach (($row['gallery'] ?? []) as $image) {
            if (! empty($image['source_file'])) {
                $this->assertStagingFile($this->stagingPath($image['source_file']));
            }
        }

        foreach (($row['inline_images'] ?? []) as $image) {
            if (empty($image['external']) && ! empty($image['source_file'])) {
                $this->assertStagingFile($this->stagingPath($image['source_file']));
            }
        }
    }

    private function storeWebp(string $source, string $target, int $maxWidth): void
    {
        $this->assertStagingFile($source);

        if ($this->dryRun) {
            return;
        }

        $contents = $this->optimizer->webpContentsFromPath($source, $maxWidth);

        if (! Storage::disk('public')->put($target, $contents)) {
            throw new RuntimeException("Gagal menulis {$target}");
        }
    }

    private function copyPageAsset(): void
    {
        $source = $this->source.'/staging/pages/dana_desa.html';

        if (! is_file($source)) {
            return;
        }

        if ($this->dryRun) {
            return;
        }

        Storage::disk('public')->put('pages/dana_desa.html', file_get_contents($source));
    }

    private function categoryIdFor(mixed $legacyId): ?int
    {
        if ($legacyId === null) {
            return $this->defaultCategoryId ?: null;
        }

        return $this->categoryIds[(int) $legacyId] ?? ($this->defaultCategoryId ?: null);
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        return Carbon::parse((string) $value);
    }

    private function syncTimestamps(string $table, int $id, ?Carbon $date): void
    {
        if ($this->dryRun || $date === null) {
            return;
        }

        DB::table($table)->where('id', $id)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    private function mediaFileName(string $sourceFile): string
    {
        $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
        $base = Str::slug(pathinfo($sourceFile, PATHINFO_FILENAME));

        return ($base !== '' ? $base : 'gambar').($extension !== '' ? '.'.$extension : '');
    }

    private function stagingPath(string $relative): string
    {
        return $this->source.'/staging/'.ltrim($relative, '/');
    }

    private function assertStagingFile(string $path): void
    {
        if (! is_file($path)) {
            throw new RuntimeException("File staging tidak ditemukan: {$path}");
        }
    }

    /**
     * @return array<string|int, mixed>
     */
    private function readJson(string $name): array
    {
        $path = $this->source.'/'.$name;

        if (! is_file($path)) {
            throw new RuntimeException("File export tidak ditemukan: {$path}");
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return is_array($data) ? $data : [];
    }

    private function recordError(string $label, Throwable $exception): void
    {
        $this->report['errors'][] = [
            'label' => $label,
            'message' => $exception->getMessage(),
        ];

        $this->error("{$label}: {$exception->getMessage()}");
    }

    private function writeReport(): void
    {
        file_put_contents(
            $this->source.'/import_report.json',
            json_encode($this->report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    private function printSummary(): void
    {
        $rows = [];

        foreach ($this->report['counts'] as $section => $counts) {
            $rows[] = [$section, $counts['processed'], $counts['created'], $counts['updated']];
        }

        $this->table(['Bagian', 'Diproses', 'Dibuat', 'Diperbarui'], $rows);
        $this->line(sprintf(
            'Media: cover=%d, lampiran=%d, galeri=%d, inline=%d',
            $this->report['media']['covers'],
            $this->report['media']['attachments'],
            $this->report['media']['gallery'],
            $this->report['media']['inline'],
        ));

        if ($this->report['errors'] !== []) {
            $this->error(sprintf('%d error. Lihat import_report.json.', count($this->report['errors'])));
        } else {
            $this->info($this->dryRun ? 'Dry-run selesai tanpa error.' : 'Impor selesai tanpa error.');
        }
    }
}
