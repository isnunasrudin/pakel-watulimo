<?php

namespace App\Filament\Resources\VillageSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VillageSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('village_name')
                    ->label('Nama desa')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(80)
                    ->wrap()
                    ->placeholder('Belum diisi'),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('Belum diisi'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
