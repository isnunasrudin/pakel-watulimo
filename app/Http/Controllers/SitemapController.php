<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Return the public sitemap.
     */
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('articles.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('gallery.photos'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('gallery.videos'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('pages.profile'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('pages.officials'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('pages.services'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('pages.contact'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        Page::query()
            ->published()
            ->orderBy('title')
            ->get(['slug', 'updated_at'])
            ->each(function (Page $page) use (&$urls): void {
                $urls[] = [
                    'loc' => route('pages.show', $page),
                    'lastmod' => $page->updated_at?->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        Article::query()
            ->published()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at'])
            ->each(function (Article $article) use (&$urls): void {
                $urls[] = [
                    'loc' => route('articles.show', $article),
                    'lastmod' => $article->updated_at?->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

        Category::query()
            ->whereHas('articles', fn (Builder $query) => $query->published())
            ->orderBy('order')
            ->get(['slug', 'updated_at'])
            ->each(function (Category $category) use (&$urls): void {
                $urls[] = [
                    'loc' => route('categories.show', $category),
                    'lastmod' => $category->updated_at?->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            });

        Album::query()
            ->published()
            ->orderBy('order')
            ->get(['slug', 'updated_at'])
            ->each(function (Album $album) use (&$urls): void {
                $urls[] = [
                    'loc' => route('gallery.album', $album),
                    'lastmod' => $album->updated_at?->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Return the robots.txt content with the sitemap location.
     */
    public function robots(): Response
    {
        return response(
            "User-agent: *\nDisallow: /admin\n\nSitemap: ".url('/sitemap.xml')."\n",
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }
}
