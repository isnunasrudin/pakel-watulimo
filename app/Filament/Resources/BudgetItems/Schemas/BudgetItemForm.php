<?php

namespace App\Filament\Resources\BudgetItems\Schemas;

use App\Models\BudgetItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class BudgetItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail APBDes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('year')
                            ->label('Tahun anggaran')
                            ->required()
                            ->numeric()
                            ->default((int) now()->year)
                            ->minValue(2000)
                            ->maxValue(2100),
                        Select::make('section')
                            ->label('Bagian')
                            ->options([
                                BudgetItem::SECTION_PENDAPATAN => 'Pendapatan',
                                BudgetItem::SECTION_BELANJA => 'Belanja',
                                BudgetItem::SECTION_PEMBIAYAAN => 'Pembiayaan',
                            ])
                            ->required(),
                        TextInput::make('group')
                            ->label('Kelompok grafik')
                            ->maxLength(255)
                            ->helperText('Dipakai sebagai label batang grafik. Bila kosong, dipakai uraian.'),
                        TextInput::make('label')
                            ->label('Uraian')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('budget_amount')
                            ->label('Anggaran')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->helperText('Pemisah ribuan otomatis; disimpan sebagai angka penuh.'),
                        TextInput::make('realization_amount')
                            ->label('Realisasi')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->helperText('Pemisah ribuan otomatis; disimpan sebagai angka penuh.'),
                        TextInput::make('order')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),
            ]);
    }
}
