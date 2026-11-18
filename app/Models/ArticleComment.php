<?php

namespace App\Models;

use Database\Factories\ArticleCommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'article_id',
    'name',
    'email',
    'body',
    'status',
    'ip_address',
    'user_agent',
])]
class ArticleComment extends Model
{
    /** @use HasFactory<ArticleCommentFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * @return BelongsTo<Article, $this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    #[Scope]
    protected function approved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
