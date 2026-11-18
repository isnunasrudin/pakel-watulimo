<?php

namespace App\Filament\Resources\Announcements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->wrap()
                    ->limit(70),
                TextColumn::make('level')
                    ->label('Tingkat')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'warning' => 'Peringatan',
                        'urgent' => 'Mendesak',
                        default => 'Informasi',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'warning' => 'warning',
                        'urgent' => 'danger',
                        default => 'info',
                    }),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Tingkat')
                    ->options([
                        'info' => 'Informasi',
                        'warning' => 'Peringatan',
                        'urgent' => 'Mendesak',
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
