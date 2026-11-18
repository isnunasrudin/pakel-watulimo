<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\BudgetItem;
use App\Models\Category;
use App\Models\HomeSlide;
use App\Models\ProgramLink;
use App\Models\SocialLink;
use App\Models\Video;
use App\Models\VillageOfficial;
use App\Models\VillagePillar;
use App\Models\VillageStatistic;
use App\Services\VillageProfileService;
use App\Services\WeatherService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the village homepage.
     */
    public function index(
        Request $request,
        WeatherService $weatherService,
        VillageProfileService $villageProfile,
    ): View {
        $articles = Article::query()
            ->published()
            ->with(['category', 'author', 'media'])
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        $announcement = Announcement::query()
            ->active()
            ->latest('starts_at')
            ->first();

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

        /**
         * @var array{
         *     statistics: list<array{label: string, value: string, unit: ?string, icon: ?string}>,
         *     officials: list<array{name: string, position: string, photo_url: string, background_color: string, phone: ?string}>,
         *     pillars: list<array{name: string, position: string, photo_url: string, background_color: string, phone: ?string}>,
         *     programLinks: list<array{title: string, image_url: ?string, background_color: string, safe_url: ?string}>,
         *     socialLinks: list<array{platform: string, icon: ?string, safe_url: ?string}>
         * } $villageContent
         */
        $villageContent = Cache::remember('home.village-content', now()->addMinutes(10), function (): array {
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

        /**
         * @var array{todayVisitors: int, activeVisitors: int, totalReaders: int} $visitorStats
         */
        $visitorStats = [
            'todayVisitors' => $this->countVisitorsSince(now('Asia/Jakarta')->startOfDay()->timestamp),
            'activeVisitors' => $this->countVisitorsSince(now()->subMinutes((int) config('session.lifetime', 120))->timestamp),
            'totalReaders' => (int) Article::query()->sum('views_count'),
        ];

        $budget = $this->budgetSummary($request);

        $homeSlides = HomeSlide::query()->with(['media', 'photo.media', 'photo.album', 'article.media'])->active()->ordered()->get();

        $weather = $weatherService->getForecast();

        $agendas = Agenda::query()
            ->published()
            ->orderBy('starts_at')
            ->get();

        /**
         * @var array{
         *     past: Collection<int, Agenda>,
         *     ongoing: Collection<int, Agenda>,
         *     upcoming: Collection<int, Agenda>
         * } $agendaGroups
         */
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

        $villageSettings = $villageProfile->settings();

        return view('pages.home', [
            'articles' => $articles,
            'announcement' => $announcement,
            'albums' => $albums,
            'videos' => $videos,
            'statistics' => $villageContent['statistics'],
            'officials' => $villageContent['officials'],
            'pillars' => $villageContent['pillars'] ?? [],
            'programLinks' => $villageContent['programLinks'],
            'socialLinks' => $villageContent['socialLinks'],
            'visitorStats' => $visitorStats,
            'budget' => $budget,
            'weather' => $weather,
            'agendaGroups' => $agendaGroups,
            'categories' => $categories,
            'articleArchives' => $articleArchives,
            'villageSettings' => $villageSettings,
            'homeCopy' => $villageSettings->homeCopy(),
            'homeSlides' => $homeSlides,
        ]);
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

    /**
     * Build the APBDes summary used by the homepage finance chart.
     *
     * @return array{
     *     year: int,
     *     availableYears: list<int>,
     *     hasData: bool,
     *     sections: array<string, array{label: string, budget: int, realization: int, percentage: float}>,
     *     groups: array<string, list<array{label: string, budget: int, realization: int}>>
     * }
     */
    private function budgetSummary(Request $request): array
    {
        $availableYears = BudgetItem::query()
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn (int|string $year): int => (int) $year)
            ->values()
            ->all();

        $requestedYear = filter_var($request->query('tahun'), FILTER_VALIDATE_INT);
        $year = is_int($requestedYear) && in_array($requestedYear, $availableYears, true)
            ? $requestedYear
            : ($availableYears[0] ?? (int) now()->year);

        $items = BudgetItem::query()
            ->where('year', $year)
            ->ordered()
            ->get();

        $sections = [];
        $sectionLabels = [
            BudgetItem::SECTION_PENDAPATAN => 'Pendapatan',
            BudgetItem::SECTION_BELANJA => 'Belanja',
            BudgetItem::SECTION_PEMBIAYAAN => 'Pembiayaan',
        ];

        foreach ($sectionLabels as $section => $label) {
            $sectionItems = $items->where('section', $section);
            $budget = (int) $sectionItems->sum('budget_amount');
            $realization = (int) $sectionItems->sum('realization_amount');

            $sections[$section] = [
                'label' => $label,
                'budget' => $budget,
                'realization' => $realization,
                'percentage' => $budget > 0 ? round($realization / $budget * 100, 1) : 0.0,
            ];
        }

        $groups = [];

        foreach ([BudgetItem::SECTION_PENDAPATAN, BudgetItem::SECTION_BELANJA] as $section) {
            $groups[$section] = $items
                ->where('section', $section)
                ->groupBy(fn (BudgetItem $item): string => $item->group ?: $item->label)
                ->map(fn (Collection $groupItems, int|string $label): array => [
                    'label' => (string) $label,
                    'budget' => (int) $groupItems->sum('budget_amount'),
                    'realization' => (int) $groupItems->sum('realization_amount'),
                ])
                ->values()
                ->all();
        }

        return [
            'year' => $year,
            'availableYears' => $availableYears,
            'hasData' => $items->isNotEmpty(),
            'sections' => $sections,
            'groups' => $groups,
        ];
    }
}
