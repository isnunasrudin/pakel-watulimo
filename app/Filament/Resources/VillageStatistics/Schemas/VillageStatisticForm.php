<?php

namespace App\Filament\Resources\VillageStatistics\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VillageStatisticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255),
                TextInput::make('value')
                    ->label('Nilai')
                    ->required()
                    ->maxLength(80)
                    ->helperText('Nilai ditampilkan apa adanya, misalnya 3.248.'),
                TextInput::make('unit')
                    ->label('Satuan')
                    ->maxLength(80),
                Select::make('group')
                    ->label('Kelompok')
                    ->options([
                        'demografi' => 'Demografi',
                        'wilayah' => 'Wilayah',
                        'pendidikan' => 'Pendidikan',
                        'ekonomi' => 'Ekonomi',
                        'umum' => 'Umum',
                    ])
                    ->required()
                    ->default('umum'),
                Select::make('icon')
                    ->label('Ikon')
                    ->options([
                        'fas fa-users' => 'Penduduk',
                        'fas fa-home' => 'Keluarga',
                        'fas fa-mars' => 'Laki-laki',
                        'fas fa-venus' => 'Perempuan',
                        'fas fa-map-marker-alt' => 'Dusun/lokasi',
                        'fas fa-map' => 'Luas wilayah',
                        'fas fa-chart-bar' => 'Statistik umum',
                    ])
                    ->searchable(),
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ])->columns(2);
    }
}
