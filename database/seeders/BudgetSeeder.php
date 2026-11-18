<?php

namespace Database\Seeders;

use App\Models\BudgetItem;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Seed the demo APBDes data that was previously hardcoded in the finance partial.
     */
    public function run(): void
    {
        $year = 2026;

        foreach ($this->items() as $item) {
            BudgetItem::updateOrCreate(
                [
                    'year' => $year,
                    'section' => $item['section'],
                    'group' => $item['group'],
                    'label' => $item['label'],
                ],
                [...$item, 'year' => $year],
            );
        }
    }

    /**
     * Get the demo budget items.
     *
     * @return list<array{
     *     section: string,
     *     group: string,
     *     label: string,
     *     budget_amount: int,
     *     realization_amount: int,
     *     order: int
     * }>
     */
    private function items(): array
    {
        return [
            [
                'section' => BudgetItem::SECTION_PENDAPATAN,
                'group' => 'Usaha Desa',
                'label' => 'Hasil Usaha Desa',
                'budget_amount' => 13_000_000,
                'realization_amount' => 0,
                'order' => 1,
            ],
            [
                'section' => BudgetItem::SECTION_PENDAPATAN,
                'group' => 'Aset Desa',
                'label' => 'Hasil Aset Desa',
                'budget_amount' => 355_665_000,
                'realization_amount' => 355_665_000,
                'order' => 2,
            ],
            [
                'section' => BudgetItem::SECTION_PENDAPATAN,
                'group' => 'Dana Desa',
                'label' => 'Dana Desa',
                'budget_amount' => 373_456_000,
                'realization_amount' => 0,
                'order' => 3,
            ],
            [
                'section' => BudgetItem::SECTION_PENDAPATAN,
                'group' => 'Bagi Hasil Pajak',
                'label' => 'Bagi Hasil Pajak dan Retribusi',
                'budget_amount' => 109_546_800,
                'realization_amount' => 0,
                'order' => 4,
            ],
            [
                'section' => BudgetItem::SECTION_PENDAPATAN,
                'group' => 'Alokasi Dana Desa',
                'label' => 'Alokasi Dana Desa',
                'budget_amount' => 597_158_000,
                'realization_amount' => 94_389_500,
                'order' => 5,
            ],
            [
                'section' => BudgetItem::SECTION_PENDAPATAN,
                'group' => 'Bunga Bank',
                'label' => 'Bunga Bank',
                'budget_amount' => 9_500_000,
                'realization_amount' => 495_822,
                'order' => 6,
            ],
            [
                'section' => BudgetItem::SECTION_BELANJA,
                'group' => 'Penyelenggaraan',
                'label' => 'Bidang Penyelenggaraan Pemerintahan Desa',
                'budget_amount' => 974_409_800,
                'realization_amount' => 67_032_649,
                'order' => 1,
            ],
            [
                'section' => BudgetItem::SECTION_BELANJA,
                'group' => 'Pembangunan',
                'label' => 'Bidang Pelaksanaan Pembangunan Desa',
                'budget_amount' => 328_368_500,
                'realization_amount' => 0,
                'order' => 2,
            ],
            [
                'section' => BudgetItem::SECTION_BELANJA,
                'group' => 'Pembinaan',
                'label' => 'Bidang Pembinaan Kemasyarakatan Desa',
                'budget_amount' => 120_697_500,
                'realization_amount' => 0,
                'order' => 3,
            ],
            [
                'section' => BudgetItem::SECTION_BELANJA,
                'group' => 'Pemberdayaan',
                'label' => 'Bidang Pemberdayaan Masyarakat Desa',
                'budget_amount' => 155_550_000,
                'realization_amount' => 0,
                'order' => 4,
            ],
            [
                'section' => BudgetItem::SECTION_BELANJA,
                'group' => 'Penanggulangan',
                'label' => 'Bidang Penanggulangan Bencana, Darurat dan Mendesak Desa',
                'budget_amount' => 18_000_000,
                'realization_amount' => 0,
                'order' => 5,
            ],
            [
                'section' => BudgetItem::SECTION_PEMBIAYAAN,
                'group' => 'Pembiayaan',
                'label' => 'Pembiayaan Neto',
                'budget_amount' => 138_700_000,
                'realization_amount' => 184_149_708,
                'order' => 1,
            ],
        ];
    }
}
