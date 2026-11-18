<?php

namespace App\Filament\Resources\Agendas\Tables;

use App\Models\Agenda;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgendasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->placeholder('—'),
                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                ToggleColumn::make('is_published')
                    ->label('Terbit'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Agenda $record): string => match (true) {
                        ! $record->is_published => 'Draft',
                        $record->isOngoing() => 'Berlangsung',
                        $record->isUpcoming() => 'Akan datang',
                        default => 'Sudah berlalu',
                    })
                    ->color(fn (Agenda $record): string => match (true) {
                        ! $record->is_published => 'gray',
                        $record->isOngoing() => 'success',
                        $record->isUpcoming() => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('is_published')
                    ->label('Status terbit')
                    ->options([
                        '1' => 'Terbit',
                        '0' => 'Draft',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
