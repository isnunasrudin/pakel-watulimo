<?php

namespace App\Filament\Resources\VillageStatistics\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\VillageStatistic;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VillageStatisticsTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Nilai')
                    ->formatStateUsing(fn (string $state, VillageStatistic $record): string => trim($state.' '.$record->unit)),
                TextColumn::make('group')
                    ->label('Kelompok')
                    ->badge()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Kelompok')
                    ->options([
                        'demografi' => 'Demografi',
                        'wilayah' => 'Wilayah',
                        'pendidikan' => 'Pendidikan',
                        'ekonomi' => 'Ekonomi',
                        'umum' => 'Umum',
                    ]),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (VillageStatistic $record): Builder => VillageStatistic::query()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
