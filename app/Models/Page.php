<?php

namespace App\Models;

use App\Models\Concerns\RegistersWebpMediaConversions;
use App\Services\AttachmentFileTypes;
use Database\Factories\PageFactory;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'legacy_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'meta_title',
    'meta_description',
    'is_published',
])]
class Page extends Model implements HasMedia, HasRichContent
{
    /** @use HasFactory<PageFactory> */
    use HasFactory, InteractsWithMedia, InteractsWithRichContent, RegistersWebpMediaConversions;

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Register the page media collection.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');

        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(AttachmentFileTypes::MIME_TYPES)
            ->useDisk('public');
    }

    /**
     * Register an optimized WebP cover variant.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addWebpConversion('webp', 1600)->performOnCollections('cover');
    }

    /**
     * Register the rich content attributes of the page.
     */
    protected function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public');
    }

    /**
     * Get the public URL of the page cover, if available.
     */
    public function coverUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('cover', 'webp');

        return filled($url) ? $url : null;
    }

    /**
     * Get a plain-text summary for meta descriptions.
     */
    public function summary(int $limit = 160): string
    {
        return Str::limit(
            trim(strip_tags($this->excerpt ?: (string) $this->renderRichContent('content'))),
            $limit,
        );
    }

    /**
     * Get the page's route key.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
