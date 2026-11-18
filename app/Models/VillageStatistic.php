<?php

namespace App\Models;

use Database\Factories\VillageStatisticFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['label', 'value', 'unit', 'icon', 'group', 'order'])]
class VillageStatistic extends Model
{
    /** @use HasFactory<VillageStatisticFactory> */
    use HasFactory;

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('home.village-content');
            Cache::forget('home.village-statistics');
        });
        static::deleted(function (): void {
            Cache::forget('home.village-content');
            Cache::forget('home.village-statistics');
        });
    }
}
