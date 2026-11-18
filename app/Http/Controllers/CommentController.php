<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    /**
     * Store a public comment that awaits moderation.
     */
    public function store(StoreCommentRequest $request, Article $article): RedirectResponse
    {
        abort_unless($article->isPublished(), 404);

        $article->comments()->create([
            'name' => $request->string('name')->trim()->value(),
            'email' => $request->string('email')->trim()->value() ?: null,
            'body' => $request->string('body')->trim()->value(),
            'status' => ArticleComment::STATUS_PENDING,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
        ]);

        return back()->with('comment_status', 'Terima kasih. Komentar Anda menunggu moderasi sebelum ditampilkan.');
    }
}
