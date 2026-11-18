<?php

namespace App\Models;

use Database\Factories\AlbumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'description', 'cover_photo_id', 'is_published', 'order'])]
class Album extends Model
{
    /** @use HasFactory<AlbumFactory> */
    use HasFactory;

    /**
     * @return HasMany<Photo, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * @return BelongsTo<Photo, $this>
     */
    public function coverPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'cover_photo_id');
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Get the public URL for the album cover or a deterministic placeholder.
     */
    public function coverUrl(): string
    {
        if ($this->coverPhoto instanceof Photo) {
            return $this->coverPhoto->url();
        }

        return 'https://picsum.photos/seed/'.$this->slug.'/800/600';
    }

    /**
     * Get the smaller WebP album cover for cards and admin thumbnails.
     */
    public function coverThumbUrl(): string
    {
        if ($this->coverPhoto instanceof Photo) {
            return $this->coverPhoto->thumbUrl();
        }

        return $this->coverUrl();
    }

    /**
     * Get the album's route key.
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
            'cover_photo_id' => 'integer',
            'is_published' => 'boolean',
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Album $album): void {
            $album->photos()->get()->each->delete();
        });
    }
}
