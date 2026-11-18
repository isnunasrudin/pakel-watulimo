<?php

namespace App\Models;

use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'slug', 'video_url', 'thumbnail', 'description', 'published_at'])]
class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Determine whether the video is visible to the public.
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Get the public thumbnail URL or a deterministic placeholder.
     */
    public function thumbnailUrl(): string
    {
        if (filled($this->thumbnail)) {
            return Storage::disk('public')->url($this->thumbnail);
        }

        return 'https://picsum.photos/seed/video-'.$this->slug.'/800/450';
    }

    /**
     * Get the video's route key.
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
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Video $video): void {
            if (filled($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
            }
        });
    }
}
