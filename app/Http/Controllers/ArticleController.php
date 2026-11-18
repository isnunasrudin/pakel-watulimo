<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a paginated list of published articles.
     */
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->published()
            ->with(['category', 'author', 'media'])
            ->when($request->string('q')->trim()->value(), fn ($query, string $search) => $query
                ->where(fn ($query) => $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")))
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('pages.articles.index', [
            'articles' => $articles,
            'search' => $request->string('q')->trim()->value(),
        ]);
    }

    /**
     * Display a single published article.
     */
    public function show(Article $article): View
    {
        abort_unless($article->isPublished(), 404);

        $article->increment('views_count');
        $article->load(['category', 'author', 'approvedComments', 'media']);

        $relatedArticles = Article::query()
            ->published()
            ->with(['category', 'author', 'media'])
            ->whereKeyNot($article->getKey())
            ->when($article->category_id, fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('pages.articles.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
