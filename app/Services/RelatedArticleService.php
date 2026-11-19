<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RelatedArticleService
{
    private const CACHE_KEY_VERSION = 'articles.related.version.v3';

    private const CACHE_TTL_MINUTES = 60;

    private const CANDIDATE_LIMIT = 150;

    private const MAX_RELATED = 3;

    private const TITLE_WEIGHT = 5;

    private const EXCERPT_WEIGHT = 2;

    private const BODY_WEIGHT = 1;

    private const BODY_TOKEN_LIMIT = 1200;

    private const CATEGORY_BONUS = 4;

    /**
     * @var list<string>
     */
    private const STOPWORDS = [
        'ada', 'adalah', 'agar', 'akan', 'atau', 'bagi', 'bahwa', 'banyak', 'baru', 'bisa',
        'dalam', 'dan', 'dapat', 'dari', 'dengan', 'di', 'dia', 'ini', 'itu', 'juga', 'karena',
        'ke', 'kepada', 'lebih', 'melalui', 'merupakan', 'masih', 'pada', 'para', 'sebagai',
        'secara', 'sedang', 'seluruh', 'setelah', 'sebelum', 'setiap', 'serta', 'sudah',
        'telah', 'tentang', 'tersebut', 'terhadap', 'tidak', 'untuk', 'yang',
        'desa', 'pakel', 'watulimo', 'trenggalek', 'kecamatan', 'kabupaten', 'jawa', 'timur',
    ];

    /**
     * @return Collection<int, Article>
     */
    public function getFor(Article $article): Collection
    {
        if (! $article->exists) {
            return new Collection;
        }

        /** @var list<int> $relatedIds */
        $relatedIds = Cache::remember(
            $this->cacheKey($article),
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn (): array => $this->findRelatedIds($article),
        );

        if ($relatedIds === []) {
            return new Collection;
        }

        $articles = Article::query()
            ->published()
            ->with(['category', 'author', 'media'])
            ->whereKey($relatedIds)
            ->get()
            ->keyBy('id');

        return new Collection(
            collect($relatedIds)
                ->map(fn (int $id): ?Article => $articles->get($id))
                ->filter()
                ->values()
                ->all(),
        );
    }

    /**
     * @return list<int>
     */
    private function findRelatedIds(Article $article): array
    {
        $candidates = Article::query()
            ->published()
            ->whereKeyNot($article->getKey())
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(self::CANDIDATE_LIMIT)
            ->get(['id', 'title', 'excerpt', 'body', 'category_id', 'published_at']);

        if ($candidates->isEmpty()) {
            return [];
        }

        $sourceTitleTokens = $this->tokens($article->title);
        $sourceExcerptTokens = $this->tokens($article->excerpt);
        $sourceBodyTokens = $this->tokens($article->body, self::BODY_TOKEN_LIMIT);
        $candidateCount = $candidates->count();

        $scored = $candidates->map(function (Article $candidate) use ($article, $sourceTitleTokens, $sourceExcerptTokens, $sourceBodyTokens, $candidateCount): array {
            $titleTokens = $this->tokens($candidate->title);
            $excerptTokens = $this->tokens($candidate->excerpt);
            $bodyTokens = $this->tokens($candidate->body, self::BODY_TOKEN_LIMIT);

            $score = (count(array_intersect($sourceTitleTokens, $titleTokens)) * self::TITLE_WEIGHT)
                + (count(array_intersect($sourceExcerptTokens, $excerptTokens)) * self::EXCERPT_WEIGHT)
                + (count(array_intersect($sourceBodyTokens, $bodyTokens)) * self::BODY_WEIGHT);

            if ($article->category_id !== null && $article->category_id === $candidate->category_id) {
                $score += self::CATEGORY_BONUS;
            }

            return [
                'id' => (int) $candidate->getKey(),
                'score' => $score,
                'fallback_rank' => $score > 0
                    ? 0
                    : (((int) $candidate->getKey() - (int) $article->getKey() + $candidateCount) % $candidateCount),
                'published_at' => $candidate->published_at?->getTimestamp() ?? 0,
            ];
        })->all();

        usort($scored, function (array $left, array $right): int {
            return ($right['score'] <=> $left['score'])
                ?: ($left['fallback_rank'] <=> $right['fallback_rank'])
                ?: ($right['published_at'] <=> $left['published_at'])
                ?: ($right['id'] <=> $left['id']);
        });

        return array_slice(array_column($scored, 'id'), 0, self::MAX_RELATED);
    }

    /**
     * @return list<string>
     */
    private function tokens(?string $text, ?int $limit = null): array
    {
        if ($text === null || $text === '') {
            return [];
        }

        $text = strip_tags($text);

        if ($limit !== null) {
            $text = Str::limit($text, $limit);
        }

        $words = preg_split('/[^\p{L}\p{N}]+/u', Str::lower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_filter(
            $words,
            fn (string $word): bool => mb_strlen($word) >= 3 && ! in_array($word, self::STOPWORDS, true),
        )));
    }

    private function cacheKey(Article $article): string
    {
        $version = (string) Cache::get(self::CACHE_KEY_VERSION, '1');

        return 'articles.related.'.$version.'.'.$article->getKey();
    }
}
