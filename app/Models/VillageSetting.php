<?php

namespace App\Models;

use App\Models\Concerns\RegistersWebpMediaConversions;
use Database\Factories\VillageSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'village_name',
    'district_name',
    'regency_name',
    'province_name',
    'description',
    'address',
    'phone',
    'email',
    'whatsapp',
    'office_hours',
    'maps_url',
    'map_embed_url',
    'hero_badge',
    'hero_title',
    'hero_highlight',
    'hero_description',
    'hero_primary_label',
    'hero_primary_url',
    'hero_secondary_label',
    'hero_secondary_url',
    'home_news_title',
    'home_news_link_label',
    'home_finance_title',
    'home_statistics_title',
])]
class VillageSetting extends Model implements HasMedia
{
    /** @use HasFactory<VillageSettingFactory> */
    use HasFactory, InteractsWithMedia, RegistersWebpMediaConversions;

    /**
     * Get the current settings record, or a safe fallback before initial seeding.
     */
    public static function current(): static
    {
        /** @var array<string, mixed> $attributes */
        $attributes = Cache::remember('village.settings.current', now()->addMinutes(10), function (): array {
            return static::query()->first()?->getAttributes() ?? [
                'village_name' => 'Desa Pakel',
                'district_name' => 'Watulimo',
                'regency_name' => 'Trenggalek',
                'province_name' => 'Jawa Timur',
            ];
        });

        $settings = (new static)->forceFill($attributes);
        $settings->exists = ! empty($attributes['id']);

        if ($settings->exists) {
            $settings->load('media');
        }

        return $settings;
    }

    /**
     * Register the visual identity media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
            ->useDisk('public');

        $this->addMediaCollection('favicon')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/svg+xml',
                'image/webp',
                'image/x-icon',
                'image/vnd.microsoft.icon',
            ])
            ->useDisk('public');

        $this->addMediaCollection('hero')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }

    /**
     * Register optimized WebP variants for raster visual identity media.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && ! in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return;
        }

        $this->addWebpConversion('webp_icon', 512, 82)->performOnCollections('icon');
        $this->addWebpConversion('webp_favicon', 256, 82)->performOnCollections('favicon');
        $this->addWebpConversion('webp_hero', 1920)->performOnCollections('hero');
    }

    /**
     * Get the uploaded logo/icon URL, if available.
     */
    public function iconUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('icon', 'webp_icon');

        return filled($url) ? $url : null;
    }

    /**
     * Get the uploaded favicon URL, falling back to the bundled favicon.
     */
    public function faviconUrl(): string
    {
        $url = $this->getFirstMediaUrl('favicon', 'webp_favicon');

        return filled($url) ? $url : asset('favicon.ico');
    }

    /**
     * Get the original favicon for apple-touch-icon, which does not support WebP.
     */
    public function appleTouchIconUrl(): string
    {
        $url = $this->getFirstMediaUrl('favicon');

        return filled($url) ? $url : asset('favicon.ico');
    }

    /**
     * Get the manually uploaded hero image URL, if available.
     */
    public function heroUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('hero', 'webp_hero');

        return filled($url) ? $url : null;
    }

    /**
     * Build the editable copy for the homepage with safe fallbacks.
     *
     * @return array{
     *     badge: string,
     *     title: string,
     *     highlight: string,
     *     description: string,
     *     primary_label: string,
     *     primary_url: string,
     *     secondary_label: string,
     *     secondary_url: string,
     *     news_heading: string,
     *     news_link_label: string,
     *     finance_heading: string,
     *     statistics_heading: string
     * }
     */
    public function homeCopy(): array
    {
        $defaultDescription = 'Mewujudkan desa yang maju, mandiri, sejahtera, dan berbudaya melalui tata kelola pemerintahan yang baik dan partisipasi aktif masyarakat.';

        return [
            'badge' => $this->copyText($this->hero_badge, 'Selamat Datang di Website Resmi'),
            'title' => $this->copyText($this->hero_title, $this->village_name ?: 'Desa Pakel'),
            'highlight' => $this->copyText(
                $this->hero_highlight,
                $this->district_name ? 'Kecamatan '.$this->district_name : 'Kecamatan Watulimo',
            ),
            'description' => $this->copyText(
                $this->hero_description,
                filled($this->description) ? Str::limit($this->description, 200) : $defaultDescription,
            ),
            'primary_label' => $this->copyText($this->hero_primary_label, 'Berita Terbaru'),
            'primary_url' => $this->safeHomeLink($this->hero_primary_url, '#berita'),
            'secondary_label' => $this->copyText($this->hero_secondary_label, 'Profil Desa'),
            'secondary_url' => $this->safeHomeLink($this->hero_secondary_url, '#profil'),
            'news_heading' => $this->copyText($this->home_news_title, 'Berita & Artikel Terbaru'),
            'news_link_label' => $this->copyText($this->home_news_link_label, 'Lihat Semua'),
            'finance_heading' => $this->copyText($this->home_finance_title, 'Infografis Keuangan Desa'),
            'statistics_heading' => $this->copyText($this->home_statistics_title, 'Statistik Desa'),
        ];
    }

    /**
     * Use the configured copy when present, otherwise use the default text.
     */
    private function copyText(?string $value, string $fallback): string
    {
        return filled($value) ? trim($value) : $fallback;
    }

    /**
     * Get a safe homepage link, falling back to the default anchor.
     */
    private function safeHomeLink(?string $url, string $fallback): string
    {
        if (! filled($url)) {
            return $fallback;
        }

        return Str::sanitizeUrl(trim($url), ['http', 'https']) ?? $fallback;
    }

    /**
     * Get a safe link for the configured map.
     */
    public function safeMapsUrl(): ?string
    {
        return Str::sanitizeUrl($this->maps_url);
    }

    /**
     * Get a safe HTTPS embed URL for the configured map.
     */
    public function safeMapEmbedUrl(): ?string
    {
        return Str::sanitizeUrl($this->map_embed_url, ['https']);
    }

    /**
     * Get a safe HTTPS URL for the configured WhatsApp contact.
     */
    public function safeWhatsappUrl(): ?string
    {
        return Str::sanitizeUrl($this->whatsapp, ['https']);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('village.settings.current');
        });
        static::deleted(function (): void {
            Cache::forget('village.settings.current');
        });
    }
}
