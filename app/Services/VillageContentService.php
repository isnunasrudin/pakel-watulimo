<?php

namespace App\Services;

use App\Models\ProgramLink;
use App\Models\SocialLink;
use App\Models\VillageOfficial;
use App\Models\VillagePillar;
use App\Models\VillageStatistic;
use Illuminate\Support\Facades\Cache;

class VillageContentService
{
    /**
     * Get the cached public village content used across the site.
     *
     * @return array{
     *     statistics: list<array{label: string, value: string, unit: ?string, icon: ?string}>,
     *     officials: list<array{name: string, position: string, photo_url: string, background_color: string, phone: ?string}>,
     *     pillars: list<array{name: string, position: string, photo_url: string, background_color: string, phone: ?string}>,
     *     programLinks: list<array{title: string, image_url: ?string, background_color: string, safe_url: ?string}>,
     *     socialLinks: list<array{platform: string, icon: ?string, safe_url: ?string}>
     * }
     */
    public function get(): array
    {
        return Cache::remember('home.village-content', now()->addMinutes(10), function (): array {
            return [
                'statistics' => VillageStatistic::query()->ordered()->get()
                    ->map(fn (VillageStatistic $statistic): array => [
                        'label' => $statistic->label,
                        'value' => $statistic->value,
                        'unit' => $statistic->unit,
                        'icon' => $statistic->icon,
                    ])
                    ->all(),
                'officials' => VillageOfficial::query()->ordered()->get()
                    ->map(fn (VillageOfficial $official): array => [
                        'name' => $official->name,
                        'position' => $official->position,
                        'photo_url' => $official->photoUrl(),
                        'background_color' => $official->backgroundColor(),
                        'phone' => $official->phone,
                    ])
                    ->all(),
                'pillars' => VillagePillar::query()->ordered()->get()
                    ->map(fn (VillagePillar $pillar): array => [
                        'name' => $pillar->name,
                        'position' => $pillar->position,
                        'photo_url' => $pillar->photoUrl(),
                        'background_color' => $pillar->backgroundColor(),
                        'phone' => $pillar->phone,
                    ])
                    ->all(),
                'programLinks' => ProgramLink::query()->with('media')->ordered()->get()
                    ->map(fn (ProgramLink $programLink): array => [
                        'title' => $programLink->title,
                        'image_url' => $programLink->imageUrl(),
                        'background_color' => $programLink->backgroundColor(),
                        'safe_url' => $programLink->safeUrl(),
                    ])
                    ->filter(fn (array $programLink): bool => filled($programLink['image_url']))
                    ->values()
                    ->all(),
                'socialLinks' => SocialLink::query()->ordered()->get()
                    ->map(fn (SocialLink $socialLink): array => [
                        'platform' => $socialLink->platform,
                        'icon' => $socialLink->icon,
                        'safe_url' => $socialLink->safeUrl(),
                    ])
                    ->all(),
            ];
        });
    }
}
