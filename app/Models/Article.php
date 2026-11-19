<?php

namespace App\Models;

use App\Models\Concerns\RegistersWebpMediaConversions;
use App\Services\AttachmentFileTypes;
use Database\Factories\ArticleFactory;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'legacy_id',
    'user_id',
    'category_id',
    'title',
    'slug',
    'excerpt',
    'body',
    'cover_image',
    'is_pinned',
    'is_featured',
    'status',
    'published_at',
    'meta_title',
    'meta_description',
])]
class Article extends Model implements HasMedia, HasRichContent
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory, InteractsWithMedia, InteractsWithRichContent, RegistersWebpMediaConversions, SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ArticleComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    /**
     * @return HasMany<ArticleComment, $this>
     */
    public function approvedComments(): HasMany
    {
        return $this->comments()->where('status', ArticleComment::STATUS_APPROVED);
    }

    /**
     * Register the media collections used by the article.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->storeConversionsOnDisk('public')
            ->useDisk('public');

        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(AttachmentFileTypes::MIME_TYPES)
            ->useDisk('public');
    }

    /**
     * Register the conversions generated for uploaded images.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addWebpConversion('thumb', 480)->performOnCollections('cover');
        $this->addWebpConversion('card', 880)->performOnCollections('cover');
    }

    /**
     * Register the rich content attributes of the article.
     */
    protected function setUpRichContent(): void
    {
        $this->registerRichContent('body')
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public');
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(fn (Builder $query) => $query
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }

    #[Scope]
    protected function pinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Determine whether the article is visible to the public.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && ($this->published_at === null || $this->published_at->isPast());
    }

    /**
     * Get the public URL of the cover image.
     */
    public function coverUrl(): string
    {
        $mediaUrl = $this->getFirstMediaUrl('cover', 'card');

        if (filled($mediaUrl)) {
            return $mediaUrl;
        }

        if (filled($this->cover_image)) {
            return Storage::disk('public')->url($this->cover_image);
        }

        return 'https://picsum.photos/seed/'.$this->slug.'/600/360';
    }

    /**
     * Get a plain-text summary for listings and meta descriptions.
     */
    public function summary(int $limit = 160): string
    {
        return Str::limit(
            trim(strip_tags($this->excerpt ?: (string) $this->renderRichContent('body'))),
            $limit,
        );
    }

    /**
     * Get the article's route key.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        $invalidateRelated = static fn (): bool => Cache::forever(
            'articles.related.version.v3',
            (string) Str::uuid(),
        );

        static::saved($invalidateRelated);
        static::deleted($invalidateRelated);
        static::restored($invalidateRelated);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'views_count' => 'integer',
        ];
    }
}
