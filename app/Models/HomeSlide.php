<?php

namespace App\Models;

use App\Models\Concerns\RegistersWebpMediaConversions;
use Database\Factories\HomeSlideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'title',
    'description',
    'link_url',
    'source_type',
    'photo_id',
    'article_id',
    'image',
    'order',
    'is_active',
])]
class HomeSlide extends Model implements HasMedia
{
    /** @use HasFactory<HomeSlideFactory> */
    use HasFactory, InteractsWithMedia, RegistersWebpMediaConversions;

    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_GALLERY = 'gallery';

    public const SOURCE_ARTICLE = 'article';

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * @return BelongsTo<Photo, $this>
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * @return BelongsTo<Article, $this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Register the media collection for manually uploaded slide images.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }

    /**
     * Register an optimized WebP slide variant.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addWebpConversion('webp', 1600)->performOnCollections('image');
    }

    /**
     * Get the label of the slide source.
     */
    public function sourceLabel(): string
    {
        return match ($this->source_type) {
            self::SOURCE_GALLERY => 'Galeri Foto',
            self::SOURCE_ARTICLE => 'Artikel',
            default => 'Upload Manual',
        };
    }

    /**
     * Get the public image URL for the slide.
     */
    public function imageUrl(): string
    {
        $sourceUrl = match ($this->source_type) {
            self::SOURCE_GALLERY => $this->photo?->url(),
            self::SOURCE_ARTICLE => $this->article?->coverUrl(),
            default => null,
        };

        if (filled($sourceUrl)) {
            return $sourceUrl;
        }

        $mediaUrl = $this->getFirstMediaUrl('image', 'webp');

        if (filled($mediaUrl)) {
            return $mediaUrl;
        }

        if (filled($this->image)) {
            if (filter_var($this->image, FILTER_VALIDATE_URL) !== false) {
                return $this->image;
            }

            return Storage::disk('public')->url($this->image);
        }

        return 'https://picsum.photos/seed/home-slide-'.($this->getKey() ?? 'default').'/900/420';
    }

    /**
     * Resolve the link target for the slide.
     */
    public function resolvedLinkUrl(): ?string
    {
        if (filled($this->link_url)) {
            return Str::sanitizeUrl($this->link_url, ['http', 'https', 'mailto', 'tel']);
        }

        return match ($this->source_type) {
            self::SOURCE_GALLERY => $this->photo?->album
                ? route('gallery.album', $this->photo->album)
                : null,
            self::SOURCE_ARTICLE => $this->article
                ? route('articles.show', $this->article)
                : null,
            default => null,
        };
    }

    /**
     * Determine whether the slide link points to an external website.
     */
    public function hasExternalLink(): bool
    {
        $link = $this->resolvedLinkUrl();

        if (! filled($link) || ! Str::startsWith($link, ['http://', 'https://'])) {
            return false;
        }

        $linkHost = parse_url($link, PHP_URL_HOST);
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        return filled($linkHost)
            && filled($appHost)
            && Str::lower($linkHost) !== Str::lower($appHost);
    }

    /**
     * Resolve the title from the selected source when no explicit title is set.
     */
    public function resolvedTitle(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return match ($this->source_type) {
            self::SOURCE_GALLERY => $this->photo?->caption
                ?: $this->photo?->album?->title
                ?: 'Galeri Desa',
            self::SOURCE_ARTICLE => $this->article?->title ?: 'Artikel Desa',
            default => 'Slide Beranda',
        };
    }

    /**
     * Resolve the description from the selected source when no explicit description is set.
     */
    public function resolvedDescription(): ?string
    {
        if (filled($this->description)) {
            return $this->description;
        }

        return match ($this->source_type) {
            self::SOURCE_GALLERY => $this->photo?->album?->description,
            self::SOURCE_ARTICLE => $this->article?->excerpt ?: $this->article?->summary(120),
            default => null,
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'photo_id' => 'integer',
            'article_id' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (HomeSlide $slide): void {
            if (filled($slide->image) && filter_var($slide->image, FILTER_VALIDATE_URL) === false) {
                Storage::disk('public')->delete($slide->image);
            }
        });
    }
}
