<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Album;
use App\Models\Article;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SidebarDataService
{
    public function __construct(
        private WeatherService $weatherService,
        private VillageContentService $villageContentService,
    ) {}

    /**
     * Get the shared public sidebar data.
     *
     * @return array{
     *     albums: \Illuminate\Database\Eloquent\Collection<int, Album>,
     *     videos: \Illuminate\Database\Eloquent\Collection<int, Video>,
     *     weather: array<string, mixed>,
     *     visitorStats: array{todayVisitors: int, activeVisitors: int, totalReaders: int},
     *     agendaGroups: array{past: Collection<int, Agenda>, ongoing: Collection<int, Agenda>, upcoming: Collection<int, Agenda>},
     *     categories: \Illuminate\Database\Eloquent\Collection<int, Category>,
     *     articleArchives: Collection<int, array{key: string, label: string, count: int}>,
     *     officials: list<array{name: string, position: string, photo_url: string, background_color: string, phone: ?string}>,
     *     pillars: list<array{name: string, position: string, photo_url: string, background_color: string, phone: ?string}>,
     *     programLinks: list<array{title: string, image_url: ?string, background_color: string, safe_url: ?string}>,
     *     socialLinks: list<array{platform: string, icon: ?string, safe_url: ?string}>
     * }
     */
    public function get(): array
    {
        $villageContent = $this->villageContentService->get();

        $albums = Album::query()
            ->published()
            ->with('coverPhoto.media')
            ->withCount('photos')
            ->orderBy('order')
            ->limit(6)
            ->get();

        $videos = Video::query()
            ->published()
            ->latest('published_at')
            ->limit(4)
            ->get();

        $agendas = Agenda::query()
            ->published()
            ->orderBy('starts_at')
            ->get();

        $agendaGroups = [
            'past' => $agendas->filter(fn (Agenda $agenda): bool => $agenda->isPast())
                ->sortByDesc('starts_at')
                ->take(3)
                ->values(),
            'ongoing' => $agendas->filter(fn (Agenda $agenda): bool => $agenda->isOngoing())
                ->take(3)
                ->values(),
            'upcoming' => $agendas->filter(fn (Agenda $agenda): bool => $agenda->isUpcoming())
                ->take(3)
                ->values(),
        ];

        $categories = Category::query()
            ->withCount(['articles' => fn (Builder $query) => $query->published()])
            ->whereHas('articles', fn (Builder $query) => $query->published())
            ->orderBy('order')
            ->orderBy('name')
            ->take(7)
            ->get();

        $articleArchives = Article::query()
            ->published()
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get(['published_at'])
            ->groupBy(fn (Article $article): string => $article->published_at->format('Y-m'))
            ->map(function (Collection $articles, string $key): array {
                $publishedAt = $articles->first()?->published_at;

                return [
                    'key' => $key,
                    'label' => $publishedAt?->locale('id')->translatedFormat('F Y') ?? $key,
                    'count' => $articles->count(),
                ];
            })
            ->take(6)
            ->values();

        return [
            'albums' => $albums,
            'videos' => $videos,
            'weather' => $this->weatherService->getForecast(),
            'visitorStats' => [
                'todayVisitors' => $this->countVisitorsSince(now('Asia/Jakarta')->startOfDay()->timestamp),
                'activeVisitors' => $this->countVisitorsSince(now()->subMinutes((int) config('session.lifetime', 120))->timestamp),
                'totalReaders' => (int) Article::query()->sum('views_count'),
            ],
            'agendaGroups' => $agendaGroups,
            'categories' => $categories,
            'articleArchives' => $articleArchives,
            'officials' => $villageContent['officials'],
            'pillars' => $villageContent['pillars'] ?? [],
            'programLinks' => $villageContent['programLinks'],
            'socialLinks' => $villageContent['socialLinks'],
        ];
    }

    /**
     * Count unique visitor IP addresses with session activity since the given Unix timestamp.
     */
    private function countVisitorsSince(int $timestamp): int
    {
        return (int) DB::table('sessions')
            ->where('last_activity', '>=', $timestamp)
            ->whereNotNull('ip_address')
            ->distinct()
            ->count('ip_address');
    }
}
