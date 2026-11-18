<?php

namespace App\Models;

use Database\Factories\BudgetItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'year',
    'section',
    'group',
    'label',
    'budget_amount',
    'realization_amount',
    'order',
])]
class BudgetItem extends Model
{
    /** @use HasFactory<BudgetItemFactory> */
    use HasFactory;

    public const SECTION_PENDAPATAN = 'pendapatan';

    public const SECTION_BELANJA = 'belanja';

    public const SECTION_PEMBIAYAAN = 'pembiayaan';

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Get the human-readable label of the budget section.
     */
    public function sectionLabel(): string
    {
        return match ($this->section) {
            self::SECTION_PENDAPATAN => 'Pendapatan',
            self::SECTION_BELANJA => 'Belanja',
            self::SECTION_PEMBIAYAAN => 'Pembiayaan',
            default => ucfirst((string) $this->section),
        };
    }

    /**
     * Get the realization percentage of this item.
     */
    public function realizationPercentage(): float
    {
        if ($this->budget_amount <= 0) {
            return 0.0;
        }

        return round($this->realization_amount / $this->budget_amount * 100, 1);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'budget_amount' => 'integer',
            'realization_amount' => 'integer',
            'order' => 'integer',
        ];
    }
}
