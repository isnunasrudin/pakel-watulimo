<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display the published articles of a category.
     */
    public function show(Category $category): View
    {
        $articles = $category->articles()
            ->published()
            ->with(['category', 'author', 'media'])
            ->latest('published_at')
            ->paginate(9);

        return view('pages.articles.index', [
            'articles' => $articles,
            'category' => $category,
            'search' => '',
        ]);
    }
}
