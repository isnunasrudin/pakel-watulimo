<?php

namespace App\Models;

use Database\Factories\AgendaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'legacy_id',
    'title',
    'slug',
    'description',
    'location',
    'starts_at',
    'ends_at',
    'is_published',
])]
class Agenda extends Model
{
    /** @use HasFactory<AgendaFactory> */
    use HasFactory;

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Determine whether the agenda has not started yet.
     */
    public function isUpcoming(): bool
    {
        return $this->starts_at?->isFuture() ?? false;
    }

    /**
     * Determine whether the agenda is currently running.
     */
    public function isOngoing(): bool
    {
        return $this->starts_at !== null
            && $this->ends_at !== null
            && $this->starts_at->lessThanOrEqualTo(now())
            && $this->ends_at->greaterThanOrEqualTo(now());
    }

    /**
     * Determine whether the agenda has already ended.
     */
    public function isPast(): bool
    {
        if ($this->ends_at !== null) {
            return $this->ends_at->isPast();
        }

        return $this->starts_at?->isPast() ?? false;
    }

    /**
     * Get a human-readable date or date range.
     */
    public function dateLabel(): string
    {
        $start = $this->starts_at?->locale('id')->translatedFormat('d M Y') ?? '—';

        if ($this->ends_at === null || $this->ends_at->isSameDay($this->starts_at)) {
            return $start;
        }

        return $start.' – '.$this->ends_at->locale('id')->translatedFormat('d M Y');
    }

    /**
     * Get the agenda's route key.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }
}
