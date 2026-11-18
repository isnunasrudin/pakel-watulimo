<?php

namespace App\Filament\Forms\Components;

use App\Services\ImageOptimizer;
use Closure;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Model;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class WebpMediaLibraryFileUpload extends SpatieMediaLibraryFileUpload
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

        $this->saveUploadedFileUsing(static function (self $component, TemporaryUploadedFile $file, ?Model $record): ?string {
            if (! $record || ! method_exists($record, 'addMediaFromString')) {
                return null;
            }

            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence) {
                return null;
            }

            $contents = app(ImageOptimizer::class)->webpContents($file, $component->getWebpMaxWidth());
            $fileName = pathinfo($component->getUploadedFileNameForStorage($file), PATHINFO_FILENAME).'.webp';

            $media = $record->addMediaFromString($contents)
                ->addCustomHeaders([
                    ...['ContentType' => 'image/webp'],
                    ...$component->getCustomHeaders(),
                ])
                ->usingFileName($fileName)
                ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->storingConversionsOnDisk($component->getConversionsDiskName() ?? '')
                ->withCustomProperties($component->getCustomProperties($file))
                ->withManipulations($component->getManipulations())
                ->withResponsiveImagesIf($component->hasResponsiveImages())
                ->withProperties($component->getProperties())
                ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName());

            return $media->getAttributeValue('uuid');
        });
    }
}
