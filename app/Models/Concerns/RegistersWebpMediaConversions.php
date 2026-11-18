<?php

namespace App\Models\Concerns;

use Spatie\Image\Enums\Constraint;
use Spatie\MediaLibrary\Conversions\Conversion;

trait RegistersWebpMediaConversions
{
    /**
     * Register an optimized WebP conversion that never upscales the source.
     */
    protected function addWebpConversion(string $name, int $width, int $quality = 82): Conversion
    {
        return $this->addMediaConversion($name)
            ->width($width, [Constraint::PreserveAspectRatio, Constraint::DoNotUpsize])
            ->format('webp')
            ->quality($quality)
            ->nonQueued();
    }
}
