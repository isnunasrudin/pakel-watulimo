<?php

namespace App\Models;

use App\Services\ImageBackgroundPalette;
use Database\Factories\VillagePillarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'position', 'photo', 'background_color', 'phone', 'order'])]
class VillagePillar extends Model
{
    /** @use HasFactory<VillagePillarFactory> */
    use HasFactory;

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Get the pillar's public photo URL or a deterministic placeholder.
     */
    public function photoUrl(): string
    {
        if (filled($this->photo)) {
            return Storage::disk('public')->url($this->photo);
        }

        return 'https://picsum.photos/seed/pillar-'.Str::slug($this->name).'/300/300';
    }

    /**
     * Get the selected background color for transparent photos.
     */
    public function backgroundColor(): string
    {
        return ImageBackgroundPalette::color($this->background_color);
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

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('home.village-content');
        });
        static::deleted(function (VillagePillar $pillar): void {
            Cache::forget('home.village-content');

            if (filled($pillar->photo)) {
                Storage::disk('public')->delete($pillar->photo);
            }
        });
    }
}
