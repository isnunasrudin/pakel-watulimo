<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'color', 'order'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get the category's route key.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the badge classes that represent the category color.
     */
    public function badgeClasses(): string
    {
        return match ($this->color) {
            'amber' => 'bg-amber-100 text-amber-700',
            'blue' => 'bg-blue-100 text-blue-700',
            'purple' => 'bg-purple-100 text-purple-700',
            'teal' => 'bg-teal-100 text-teal-700',
            'red' => 'bg-red-100 text-red-700',
            'indigo' => 'bg-indigo-100 text-indigo-700',
            'pink' => 'bg-pink-100 text-pink-700',
            'yellow' => 'bg-yellow-100 text-yellow-700',
            'sky' => 'bg-sky-100 text-sky-700',
            default => 'bg-green-100 text-green-700',
        };
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
}
