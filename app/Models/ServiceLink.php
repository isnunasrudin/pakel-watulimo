<?php

namespace App\Models;

use Database\Factories\ServiceLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Fillable(['title', 'description', 'icon', 'url', 'order'])]
class ServiceLink extends Model
{
    /** @use HasFactory<ServiceLinkFactory> */
    use HasFactory;

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
            Cache::forget('public.service-links');
        });
        static::deleted(function (): void {
            Cache::forget('home.village-content');
            Cache::forget('public.service-links');
        });
    }
}
