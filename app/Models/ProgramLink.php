<?php

namespace App\Models;

use App\Models\Concerns\RegistersWebpMediaConversions;
use App\Services\ImageBackgroundPalette;
use Database\Factories\ProgramLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['title', 'description', 'icon', 'url', 'background_color', 'order'])]
class ProgramLink extends Model implements HasMedia
{
    /** @use HasFactory<ProgramLinkFactory> */
    use HasFactory, InteractsWithMedia, RegistersWebpMediaConversions;

    /**
     * Register the full-width program image collection.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }

    /**
     * Register the optimized WebP program image.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addWebpConversion('webp', 1600)->performOnCollections('image');
    }

    /**
     * Get the program image URL.
     */
    public function imageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('image', 'webp');

        return filled($url) ? $url : null;
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    /**
     * Get the selected background color for transparent images.
     */
    public function backgroundColor(): string
    {
        return ImageBackgroundPalette::color($this->background_color);
    }

    /**
     * Get a safe URL for rendering in a link.
     */
    public function safeUrl(): ?string
    {
        return Str::sanitizeUrl($this->url, ['http', 'https', 'mailto', 'tel']);
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('home.village-content');
        });
        static::deleted(function (): void {
            Cache::forget('home.village-content');
        });
        Media::created(function (Media $media): void {
            if ($media->model_type === self::class) {
                Cache::forget('home.village-content');
            }
        });
        Media::deleted(function (Media $media): void {
            if ($media->model_type === self::class) {
                Cache::forget('home.village-content');
            }
        });
    }
}
