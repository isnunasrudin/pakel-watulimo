<?php

namespace App\Models;

use App\Models\Concerns\RegistersWebpMediaConversions;
use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['album_id', 'path', 'caption', 'taken_at', 'order'])]
class Photo extends Model implements HasMedia
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory, InteractsWithMedia, RegistersWebpMediaConversions;

    /**
     * @return BelongsTo<Album, $this>
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Register the media collection used for the photo file.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }

    /**
     * Register optimized WebP variants for gallery photos.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addWebpConversion('thumb', 600)->performOnCollections('photo');
        $this->addWebpConversion('large', 1600)->performOnCollections('photo');
    }

    /**
     * Get the public URL for the photo.
     */
    public function url(): string
    {
        $mediaUrl = $this->getFirstMediaUrl('photo', 'large');

        if (filled($mediaUrl)) {
            return $mediaUrl;
        }

        if (filled($this->path)) {
            if ($this->isExternalUrl()) {
                return $this->path;
            }

            return Storage::disk('public')->url($this->path);
        }

        return 'https://picsum.photos/seed/photo-'.$this->getKey().'/800/600';
    }

    /**
     * Get the smaller WebP URL for gallery cards and admin thumbnails.
     */
    public function thumbUrl(): string
    {
        $mediaUrl = $this->getFirstMediaUrl('photo', 'thumb');

        return filled($mediaUrl) ? $mediaUrl : $this->url();
    }

    /**
     * Determine whether the photo path is an absolute HTTP(S) URL.
     */
    public function isExternalUrl(): bool
    {
        $scheme = strtolower((string) parse_url($this->path, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true)
            && filter_var($this->path, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'album_id' => 'integer',
            'taken_at' => 'datetime',
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Photo $photo): void {
            Album::query()
                ->whereKey($photo->album_id)
                ->whereNull('cover_photo_id')
                ->update(['cover_photo_id' => $photo->getKey()]);
        });

        static::deleting(function (Photo $photo): void {
            $album = Album::query()->find($photo->album_id);

            if ($album?->cover_photo_id === $photo->getKey()) {
                $nextCoverPhotoId = $album->photos()
                    ->whereKeyNot($photo->getKey())
                    ->orderBy('order')
                    ->orderBy('id')
                    ->value('id');

                $album->forceFill(['cover_photo_id' => $nextCoverPhotoId])->save();
            }

            if (filled($photo->path) && ! $photo->isExternalUrl()) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }
}
