<?php

namespace Database\Factories;

use App\Models\BudgetItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetItem>
 */
class BudgetItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $section = fake()->randomElement([
            BudgetItem::SECTION_PENDAPATAN,
            BudgetItem::SECTION_BELANJA,
            BudgetItem::SECTION_PEMBIAYAAN,
        ]);

        $groups = [
            BudgetItem::SECTION_PENDAPATAN => ['Dana Desa', 'Alokasi Dana Desa', 'Bagi Hasil Pajak'],
            BudgetItem::SECTION_BELANJA => ['Penyelenggaraan', 'Pembangunan', 'Pemberdayaan'],
            BudgetItem::SECTION_PEMBIAYAAN => ['Pembiayaan'],
        ];

        $budgetAmount = fake()->numberBetween(1_000_000, 500_000_000);

        return [
            'year' => (int) now()->year,
            'section' => $section,
            'group' => fake()->randomElement($groups[$section]),
            'label' => fake()->sentence(3),
            'budget_amount' => $budgetAmount,
            'realization_amount' => fake()->numberBetween(0, $budgetAmount),
            'order' => fake()->numberBetween(1, 20),
        ];
    }
}
