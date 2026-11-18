<?php

namespace App\Filament\Forms\Components;

use App\Services\ImageOptimizer;
use Closure;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class WebpFileUpload extends FileUpload
{
    protected int|Closure|null $webpMaxWidth = null;

    /**
     * Set the maximum width used before converting the upload to WebP.
     */
    public function webpMaxWidth(int|Closure|null $width): static
    {
        $this->webpMaxWidth = $width;

        return $this;
    }

    public function getWebpMaxWidth(): int
    {
        return (int) ($this->evaluate($this->webpMaxWidth) ?? ImageOptimizer::DEFAULT_MAX_WIDTH);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(static function (self $component, TemporaryUploadedFile $file): ?string {
            $path = app(ImageOptimizer::class)->storeUploadedFile(
                $file,
                $component->getDiskName(),
                $component->getDirectory(),
                $component->getWebpMaxWidth(),
            );

            if ($component->getVisibility() === 'public') {
                rescue(
                    fn () => Storage::disk($component->getDiskName())->setVisibility($path, 'public'),
                    report: false,
                );
            }

            return $path;
        });
    }
}
