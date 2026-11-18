<?php

namespace App\Providers;

use App\Models\HeaderMenuItem;
use App\Models\ServiceLink;
use App\Models\SocialLink;
use App\Services\VillageProfileService;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Kisame76\FilamentAdvancedRichEditor\RichEditor\AdvancedRichContentRenderer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(VillageProfileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AdvancedRichContentRenderer::bind();

        $this->configureLocalDiagnostics();

        FilamentAsset::register([
            Css::make('article-rich-editor-resizable', resource_path('css/article-rich-editor-resizable.css')),
        ]);

        ViewFacade::composer('partials.home.navbar', function (View $view): void {
            $headerMenu = Cache::remember('public.header-menu', now()->addMinutes(10), function (): array {
                return HeaderMenuItem::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->with(['children' => fn (HasMany $query): HasMany => $query->active()->ordered()])
                    ->ordered()
                    ->get()
                    ->map(fn (HeaderMenuItem $item): array => [
                        'label' => $item->label,
                        'url' => $item->safeUrl() ?? '#',
                        'new_tab' => $item->open_in_new_tab,
                        'children' => $item->children
                            ->map(fn (HeaderMenuItem $child): array => [
                                'label' => $child->label,
                                'url' => $child->safeUrl() ?? '#',
                                'new_tab' => $child->open_in_new_tab,
                            ])
                            ->all(),
                    ])
                    ->all();
            });

            $view->with([
                'villageSettings' => app(VillageProfileService::class)->settings(),
                'headerMenu' => $headerMenu,
            ]);
        });

        ViewFacade::composer('partials.home.footer', function (View $view): void {
            $serviceLinks = Cache::remember('public.service-links', now()->addMinutes(10), fn (): array => ServiceLink::query()->ordered()->get()
                ->map(fn (ServiceLink $serviceLink): array => [
                    'title' => $serviceLink->title,
                    'safe_url' => $serviceLink->safeUrl(),
                ])
                ->all()
            );
            $socialLinks = Cache::remember('public.social-links', now()->addMinutes(10), fn (): array => SocialLink::query()->ordered()->get()
                ->map(fn (SocialLink $socialLink): array => [
                    'platform' => $socialLink->platform,
                    'icon' => $socialLink->icon,
                    'safe_url' => $socialLink->safeUrl(),
                ])
                ->all()
            );

            $view->with([
                'villageSettings' => app(VillageProfileService::class)->settings(),
                'serviceLinks' => $serviceLinks,
                'socialLinks' => $socialLinks,
            ]);
        });
    }

    /**
     * Enable local-only query and lazy-loading diagnostics for public pages.
     */
    private function configureLocalDiagnostics(): void
    {
        if (app()->isProduction()) {
            return;
        }

        if (! app()->runningInConsole()) {
            Model::preventLazyLoading(! request()->is('admin', 'admin/*'));
        }

        DB::listen(function (QueryExecuted $query): void {
            if ($query->time < 500) {
                return;
            }

            Log::warning('Query lambat terdeteksi.', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time,
            ]);
        });
    }
}
